<?php

namespace Tests\Unit\Models;

use App\Models\RecursosM;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Support\CreatesApocalipseSchema;
use Tests\TestCase;

class RecursosMTest extends TestCase {

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
    public function retorna_pontos_do_recurso_existente() {
        $model = new RecursosM();

        $this->assertSame(4, $model->retornarQuantidadePontos(1)); // agua
        $this->assertSame(1, $model->retornarQuantidadePontos(4)); // municao
    }

    #[Test]
    public function retorna_zero_para_recurso_inexistente() {
        $model = new RecursosM();

        $this->assertSame(0, $model->retornarQuantidadePontos(999));
    }
}

