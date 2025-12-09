<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Feature\Support\CreatesApocalipseSchema;
use Illuminate\Support\Facades\DB;

class InformarZumbificacaoControllerTest extends TestCase {

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
    public function nao_pode_informante_zumbi() {
        // Carlos (3) é zumbi e tenta denunciar Bruno (2)
        $this->postJson('/api/informar-zumbificacao', [
                    'id_informante' => 3,
                    'id_sobrevivente' => 2,
                ])->assertUnprocessable()
                ->assertJsonFragment(['code' => 'INFORMANTE_ZUMBI']);
    }

    #[Test]
    public function nao_pode_denunciar_quem_ja_esta_contaminado() {
        // Ana (1) denuncia Carlos (3) que já é zumbi
        $this->postJson('/api/informar-zumbificacao', [
                    'id_informante' => 1,
                    'id_sobrevivente' => 3,
                ])->assertConflict()
                ->assertJsonFragment(['code' => 'SOBREVIVENTE_JA_CONTAMINADO']);
    }

    #[Test]
    public function nao_pode_duplicar_aviso() {
        // Ana denuncia Bruno duas vezes
        $payload = ['id_informante' => 1, 'id_sobrevivente' => 2];

        // 1a vez (ok ou em seguida será duplicado)
        $this->postJson('/api/informar-zumbificacao', $payload)
                ->assertCreated();

        // 2a vez (duplicado)
        $this->postJson('/api/informar-zumbificacao', $payload)
                ->assertConflict()
                ->assertJsonFragment(['code' => 'AVISO_DUPLICADO']);
    }

    #[Test]
    public function ao_atingir_tres_avisos_o_alvo_vira_zumbi() {
        // Três pessoas diferentes denunciam Bruno (2)
        // Já temos Ana(1) e Carlos(3-zumbi) não pode; então criamos mais um sobrevivente 4
        DB::table('tbsobreviventes')->insert([
            'id' => 4, 'nome' => 'Debora', 'sexo' => 'F', 'idade' => 26, 'latitude' => -3.71, 'longitude' => -38.59, 'zumbi' => 0
        ]);

        // 1º aviso: Ana -> Bruno
        $this->postJson('/api/informar-zumbificacao', ['id_informante' => 1, 'id_sobrevivente' => 2])->assertCreated();

        // 2º aviso: Debora -> Bruno
        $this->postJson('/api/informar-zumbificacao', ['id_informante' => 4, 'id_sobrevivente' => 2])->assertCreated();

        // 3º aviso: criaremos mais um sobrevivente para o terceiro informante
        DB::table('tbsobreviventes')->insert([
            'id' => 5, 'nome' => 'Elton', 'sexo' => 'M', 'idade' => 33, 'latitude' => -3.72, 'longitude' => -38.58, 'zumbi' => 0
        ]);
        $this->postJson('/api/informar-zumbificacao', ['id_informante' => 5, 'id_sobrevivente' => 2])->assertCreated();

        $isZumbi = DB::table('tbsobreviventes')->where('id', 2)->value('zumbi');
        $this->assertSame(1, (int) $isZumbi);
    }
}
