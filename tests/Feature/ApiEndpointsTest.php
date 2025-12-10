<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Coberto por outros testes de feature específicos.');
    }

    public function test_list_sobreviventes(): void
    {
        $this->getJson('/api/sobreviventes')->assertOk();
    }

    public function test_relatorio(): void
    {
        $this->getJson('/api/relatorio-geral')->assertOk();
    }
}
