<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Support\CreatesApocalipseSchema;
use Tests\TestCase;

class SobreviventesControllerTest extends TestCase {

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
    public function index_lista_todos_sobreviventes() {
        $this->getJson('/api/sobreviventes')
                ->assertOk()
                ->assertJsonCount(3); // inclui o zumbi tambem (o controller usa ::all())
    }

    #[Test]
    public function show_retorna_um_sobrevivente_por_id() {
        $this->getJson('/api/sobreviventes/1')
                ->assertOk()
                ->assertJsonFragment(['id' => 1, 'nome' => 'Ana']);
    }

    #[Test]
    public function store_cria_sobrevivente_e_inventario() {
        $payload = [
            'nome' => 'Diana',
            'idade' => 22,
            'sexo' => 'F',
            'latitude' => -3.75,
            'longitude' => -38.55,
            'zumbi' => 0,
            'id_recurso' => [1, 2],
            'quantidade' => [3, 7],
        ];

        $this->postJson('/api/sobreviventes', $payload)
                ->assertCreated()
                ->assertJsonFragment(['status' => 'OK']);

        $this->assertDatabaseHas('tbsobreviventes', ['nome' => 'Diana']);
        $id = (int) DB::table('tbsobreviventes')->where('nome', 'Diana')->value('id');

        $this->assertDatabaseHas('tbsobreviventes_recursos', [
            'id_sobrevivente' => $id, 'id_recurso' => 1, 'quantidade' => 3
        ]);
        $this->assertDatabaseHas('tbsobreviventes_recursos', [
            'id_sobrevivente' => $id, 'id_recurso' => 2, 'quantidade' => 7
        ]);
    }

    #[Test]
    public function update_bloqueia_quando_sobrevivente_e_zumbi() {
        // Carlos e zumbi (id=3)
        $this->putJson('/api/sobreviventes/3', [
                    'latitude' => -3.1, 'longitude' => -38.1
                ])->assertUnprocessable()
                ->assertSee('Zumbi');
    }

    #[Test]
    public function update_atualiza_quando_nao_e_zumbi() {
        $this->putJson('/api/sobreviventes/1', [
                    'latitude' => -3.11, 'longitude' => -38.11
                ])->assertOk()
                ->assertSee('OK');

        $this->assertDatabaseHas('tbsobreviventes', [
            'id' => 1, 'latitude' => -3.11, 'longitude' => -38.11
        ]);
    }

    #[Test]
    public function destroy_remove_sobrevivente_e_inventario() {
        $this->deleteJson('/api/sobreviventes/2')
                ->assertOk()
                ->assertSee('OK');

        $this->assertDatabaseMissing('tbsobreviventes', ['id' => 2]);
        $this->assertDatabaseMissing('tbsobreviventes_recursos', ['id_sobrevivente' => 2]);
    }
}
