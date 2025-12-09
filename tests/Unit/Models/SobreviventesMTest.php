<?php

namespace Tests\Unit\Models;

use App\Models\SobreviventesM;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Support\CreatesApocalipseSchema;
use Tests\TestCase;

class SobreviventesMTest extends TestCase {

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
    public function remover_sobrevivente_apaga_registros_relacionados() {
        $model = new SobreviventesM();

        $this->assertTrue($model->removerSobrevivente(2));

        $this->assertDatabaseMissing('tbsobreviventes', ['id' => 2]);
        $this->assertDatabaseMissing('tbsobreviventes_recursos', ['id_sobrevivente' => 2]);
    }

    #[Test]
    public function informar_contaminacao_marca_como_zumbi_apos_tres_avisos() {
        $model = new SobreviventesM();

        // cria dois informantes extras para chegar aos 3 avisos
        DB::table('tbsobreviventes')->insert([
            ['id' => 4, 'nome' => 'Debora', 'sexo' => 'F', 'idade' => 26, 'latitude' => -3.71, 'longitude' => -38.59, 'zumbi' => 0],
            ['id' => 5, 'nome' => 'Elton', 'sexo' => 'M', 'idade' => 33, 'latitude' => -3.72, 'longitude' => -38.58, 'zumbi' => 0],
        ]);

        $model->informarContaminacao(1, 2);
        $model->informarContaminacao(4, 2);
        $model->informarContaminacao(5, 2);

        $isZumbi = DB::table('tbsobreviventes')->where('id', 2)->value('zumbi');
        $this->assertSame(1, (int) $isZumbi);

        $this->assertSame(3, $model->verificarExisteNotificacaoContaminacaoTotal(2));
    }

    #[Test]
    public function retornar_sobreviventes_lista_apenas_nao_zumbis() {
        $lista = (new SobreviventesM())->retornarSobreviventes();

        $this->assertCount(2, $lista);
        $ids = $lista->pluck('id')->all();
        $this->assertNotContains(3, $ids); // id=3 e zumbi
    }

    #[Test]
    public function relatorio_infectados_retorna_totais() {
        $resumo = (new SobreviventesM())->retornarRelatorioInfectados();

        $this->assertSame(1, $resumo['infectados']);
        $this->assertSame(2, $resumo['nao_infectados']);
    }
}

