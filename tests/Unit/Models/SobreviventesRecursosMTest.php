<?php

namespace Tests\Unit\Models;

use App\Models\SobreviventesRecursosM;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Support\CreatesApocalipseSchema;
use Tests\TestCase;

class SobreviventesRecursosMTest extends TestCase {

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
    public function retorna_recursos_de_um_sobrevivente() {
        $model = new SobreviventesRecursosM();

        $recursos = $model->retornarRecursosSobrevivente(1);

        $this->assertCount(3, $recursos); // agua, comida, remedio
        $quantidades = array_map(fn($item) => $item->quantidade, $recursos);
        $this->assertEqualsCanonicalizing([5, 4, 2], $quantidades);
        $this->assertIsString($recursos[0]->recurso);
    }

    #[Test]
    public function retornar_quantidade_recursos_respeita_valores_ausentes() {
        $model = new SobreviventesRecursosM();

        $this->assertSame(10, $model->retornarQuantidadeRecursos(2, 4)); // municao do Bruno
        $this->assertSame(0, $model->retornarQuantidadeRecursos(99, 1));
    }

    #[Test]
    public function realizar_troca_atualiza_inventarios() {
        $model = new SobreviventesRecursosM();

        $result = $model->realizarTroca(
                [1, 2],
                [
                    [(object) ['id_recurso' => 1, 'quantidade' => 1]],
                    [(object) ['id_recurso' => 4, 'quantidade' => 4]],
                ]
        );

        $this->assertTrue($result);
        $this->assertSame(4, (int) DB::table('tbsobreviventes_recursos')->where(['id_sobrevivente' => 1, 'id_recurso' => 1])->value('quantidade'));
        $this->assertSame(6, (int) DB::table('tbsobreviventes_recursos')->where(['id_sobrevivente' => 2, 'id_recurso' => 4])->value('quantidade'));
        $this->assertSame(2, (int) DB::table('tbsobreviventes_recursos')->where(['id_sobrevivente' => 2, 'id_recurso' => 1])->value('quantidade'));
        $this->assertSame(4, (int) DB::table('tbsobreviventes_recursos')->where(['id_sobrevivente' => 1, 'id_recurso' => 4])->value('quantidade'));
    }

    #[Test]
    public function realizar_troca_dispara_excecao_quando_sem_estoque() {
        $model = new SobreviventesRecursosM();

        $this->expectException(\RuntimeException::class);

        $model->realizarTroca(
                [1, 2],
                [
                    [(object) ['id_recurso' => 1, 'quantidade' => 999]], // maior que o estoque atual
                    [(object) ['id_recurso' => 4, 'quantidade' => 1]],
                ]
        );
    }

    #[Test]
    public function relatorio_recursos_retorna_objetos_tipados() {
        $model = new SobreviventesRecursosM();

        $relatorio = $model->retornarRelatorioRecursos();

        $this->assertNotEmpty($relatorio);
        $primeiro = $relatorio[0];
        $this->assertTrue(property_exists($primeiro, 'sobrevivente'));
        $this->assertTrue(property_exists($primeiro, 'recurso'));
        $this->assertIsInt($primeiro->quantidade);
        $this->assertIsFloat($primeiro->porcentagem);
    }
}
