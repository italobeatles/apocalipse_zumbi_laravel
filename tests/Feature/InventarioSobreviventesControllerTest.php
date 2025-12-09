<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Feature\Support\CreatesApocalipseSchema;
use Illuminate\Support\Facades\DB;

class InventarioSobreviventesControllerTest extends TestCase {

    use CreatesApocalipseSchema;

    protected function setUp(): void {
        parent::setUp();
        $this->createSchema();
        $this->seedBasicData();
    }

    protected function tearDown(): void {
        $this->dropSchema();
        parent::tearDown();
    }

    #[Test]
    public function index_lista_apenas_nao_zumbis_com_recursos() {
        $this->getJson('/api/inventario')
                ->assertOk()
                ->assertJsonCount(2) // Ana e Bruno (Carlos é zumbi)
                ->assertJsonFragment(['nome' => 'Ana'])
                ->assertJsonFragment(['nome' => 'Bruno']);
    }

    #[Test]
    public function show_retorna_um_nao_zumbi_com_recursos() {
        $this->getJson('/api/inventario/1')
                ->assertOk()
                ->assertJsonFragment(['id' => 1, 'nome' => 'Ana'])
                ->assertJsonFragment(['recurso' => 'Água']);
    }

    #[Test]
    public function show_nao_encontra_zumbi() {
        $this->getJson('/api/inventario/3')
                ->assertNotFound()
                ->assertJsonFragment(['message' => 'Sobrevivente não encontrado ou é zumbi']);
    }

    #[Test]
    public function troca_falha_se_pontuacao_diferente() {
        // Ana entrega 1x Água (4 pontos) ; Bruno entrega 1x Munição (1 ponto) => 4 != 1
        $payload = [
            'id_sobrevivente' => [1, 2],
            'recursos_troca' => [
                json_encode([['id_recurso' => 1, 'quantidade' => 1]]),
                json_encode([['id_recurso' => 4, 'quantidade' => 1]]),
            ],
        ];

        $this->postJson('/api/inventario/troca', $payload)
                ->assertUnprocessable()
                ->assertJsonFragment(['mensagem' => 'A quantidade de pontos dos recursos oferecidos para a troca não são iguais!']);
    }

    #[Test]
    public function troca_falha_se_algum_e_zumbi() {
        $payload = [
            'id_sobrevivente' => [1, 3], // 3 é zumbi
            'recursos_troca' => [
                json_encode([['id_recurso' => 1, 'quantidade' => 1]]),
                json_encode([['id_recurso' => 2, 'quantidade' => 2]]), // 2x comida = 6 pts; 1x água = 4 pts (nem chega na pontuação)
            ],
        ];

        $this->postJson('/api/inventario/troca', $payload)
                ->assertUnprocessable()
                ->assertJsonFragment(['mensagem' => 'Pelo menos um dos dois envolvidos na troca está contaminado!']);
    }

    #[Test]
    public function troca_ok_quando_pontuacao_igual_e_estoque_suficiente() {
        // Ana entrega: 1x Água (4 pts) ; Bruno entrega: 4x Munição (4 pts)
        $payload = [
            'id_sobrevivente' => [1, 2],
            'recursos_troca' => [
                [['id_recurso' => 1, 'quantidade' => 1]],
                [['id_recurso' => 4, 'quantidade' => 4]],
            ],
        ];

        $this->postJson('/api/inventario/troca', $payload)
                ->assertOk()
                ->assertJsonFragment(['status' => 'sucesso']);

        // Inventários atualizados?
        $aguaAna = DB::table('tbsobreviventes_recursos')->where(['id_sobrevivente' => 1, 'id_recurso' => 1])->value('quantidade'); // 5-1 = 4
        $muniBruno = DB::table('tbsobreviventes_recursos')->where(['id_sobrevivente' => 2, 'id_recurso' => 4])->value('quantidade'); // 10-4 = 6
        $aguaBruno = DB::table('tbsobreviventes_recursos')->where(['id_sobrevivente' => 2, 'id_recurso' => 1])->value('quantidade'); // 1+1 = 2
        $muniAna = DB::table('tbsobreviventes_recursos')->where(['id_sobrevivente' => 1, 'id_recurso' => 4])->value('quantidade'); // 0+4 = 4 (linha criada)

        $this->assertSame(4, (int) $aguaAna);
        $this->assertSame(6, (int) $muniBruno);
        $this->assertSame(2, (int) $aguaBruno);
        $this->assertSame(4, (int) $muniAna);
    }
}
