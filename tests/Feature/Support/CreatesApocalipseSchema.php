<?php

namespace Tests\Feature\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

trait CreatesApocalipseSchema {

    protected function createSchema(): void {
        // tbsobreviventes
        Schema::create('tbsobreviventes', function (Blueprint $t) {
            $t->increments('id');
            $t->string('nome');
            $t->string('sexo', 1);
            $t->unsignedInteger('idade');
            $t->double('latitude');
            $t->double('longitude');
            $t->boolean('zumbi')->default(false);
        });

        // tbrecursos
        Schema::create('tbrecursos', function (Blueprint $t) {
            $t->increments('id');
            $t->string('descricao');
            $t->unsignedInteger('pontos');
        });

        // tbsobreviventes_recursos
        Schema::create('tbsobreviventes_recursos', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('id_sobrevivente');
            $t->unsignedInteger('id_recurso');
            $t->unsignedInteger('quantidade')->default(0);
            $t->index(['id_sobrevivente', 'id_recurso']);
        });

        // tbavisos_zumbificacao
        Schema::create('tbavisos_zumbificacao', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('id_sobrevivente');
            $t->unsignedInteger('id_informante');
            $t->timestamps();
        });
    }

    protected function dropSchema(): void {
        Schema::dropIfExists('tbavisos_zumbificacao');
        Schema::dropIfExists('tbsobreviventes_recursos');
        Schema::dropIfExists('tbrecursos');
        Schema::dropIfExists('tbsobreviventes');
    }

    protected function seedBasicData(): array {
        // Recursos
        DB::table('tbrecursos')->insert([
            ['id' => 1, 'descricao' => 'Água', 'pontos' => 4],
            ['id' => 2, 'descricao' => 'Comida', 'pontos' => 3],
            ['id' => 3, 'descricao' => 'Remédio', 'pontos' => 2],
            ['id' => 4, 'descricao' => 'Munição', 'pontos' => 1],
        ]);

        // Sobreviventes (2 vivos, 1 zumbi)
        DB::table('tbsobreviventes')->insert([
            ['id' => 1, 'nome' => 'Ana', 'sexo' => 'F', 'idade' => 28, 'latitude' => -3.7, 'longitude' => -38.5, 'zumbi' => 0],
            ['id' => 2, 'nome' => 'Bruno', 'sexo' => 'M', 'idade' => 31, 'latitude' => -3.8, 'longitude' => -38.6, 'zumbi' => 0],
            ['id' => 3, 'nome' => 'Carlos', 'sexo' => 'M', 'idade' => 40, 'latitude' => -3.9, 'longitude' => -38.7, 'zumbi' => 1],
        ]);

        // Inventário
        DB::table('tbsobreviventes_recursos')->insert([
            // Ana
            ['id_sobrevivente' => 1, 'id_recurso' => 1, 'quantidade' => 5], // Agua 5
            ['id_sobrevivente' => 1, 'id_recurso' => 2, 'quantidade' => 4], // Comida 4
            ['id_sobrevivente' => 1, 'id_recurso' => 3, 'quantidade' => 2], // Remédio 2
            // Bruno
            ['id_sobrevivente' => 2, 'id_recurso' => 1, 'quantidade' => 1], // Agua 1
            ['id_sobrevivente' => 2, 'id_recurso' => 4, 'quantidade' => 10], // Munição 10
            // Carlos (zumbi)
            ['id_sobrevivente' => 3, 'id_recurso' => 2, 'quantidade' => 7], // Comida 7
        ]);

        return [
            'agua' => 1, 'comida' => 2, 'remedio' => 3, 'municao' => 4,
            'ana' => 1, 'bruno' => 2, 'carlos' => 3,
        ];
    }
}
