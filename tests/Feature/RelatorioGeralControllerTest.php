<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Feature\Support\CreatesApocalipseSchema;
use Illuminate\Support\Facades\DB;

class RelatorioGeralControllerTest extends TestCase {

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
    public function relatorio_geral_ok() {
        $res = $this->getJson('/api/relatorio-geral')
                ->assertOk()
                ->json();

        // Chaves de topo
        $this->assertArrayHasKey('disponibilidade_recursos', $res);
        $this->assertArrayHasKey('balanco_infectados', $res);

        // Disponibilidade por pessoa deve conter Ana e Bruno (apenas não-zumbis)
        $disp = $res['disponibilidade_recursos']['disponibilidade_de_recursos_por_pessoa'] ?? [];
        $this->assertArrayHasKey('Ana', $disp);
        $this->assertArrayHasKey('Bruno', $disp);

        // Balanço infectados
        $balanco = $res['balanco_infectados'];
        $this->assertSame(3, (int) $balanco['total']); // 2 vivos + 1 zumbi = 3
        $this->assertIsNumeric($balanco['infectados_porcentagem']);
        $this->assertIsNumeric($balanco['nao_infectados_porcentagem']);
    }

    #[Test]
    public function relatorio_geral_sem_sobreviventes_nao_explode_divisao_por_zero() {
        // Zera as tabelas
        DB::table('tbsobreviventes_recursos')->truncate();
        DB::table('tbsobreviventes')->truncate();

        $res = $this->getJson('/api/relatorio-geral')
                ->assertOk()
                ->json();

        $balanco = $res['balanco_infectados'];
        $this->assertSame(0, (int) $balanco['total']);
        $this->assertSame(0.0, (float) $balanco['infectados_porcentagem']);
        $this->assertSame(0.0, (float) $balanco['nao_infectados_porcentagem']);
    }
}
