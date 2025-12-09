<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_sobreviventes(): void
    {
        $this->getJson('/api/sobreviventes')->assertOk();
    }

    public function test_relatorio(): void
    {
        $this->getJson('/api/relatorio-geral')->assertOk();
    }
}
