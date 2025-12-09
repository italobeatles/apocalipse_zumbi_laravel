<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecursosM extends Model {

    protected $table = 'tbrecursos';
    public $timestamps = false;
    // Eloquent já assume chave primária "id" e auto-incremento
    // Então não inclua 'id' no fillable:
    protected $fillable = ['descricao', 'pontos'];
    // Converte automaticamente tipos quando acessados/atribuídos
    protected $casts = [
        'id' => 'integer',
        'pontos' => 'integer',
        'descricao' => 'string',
    ];

    /**
     * Retorna a quantidade de pontos de um recurso.
     */
    public function retornarQuantidadePontos(int $id): int {
        // value() faz SELECT ... LIMIT 1 e retorna só a coluna, ou null
        return (int) ($this->newQuery()
                        ->whereKey($id)        // equivalente a where('id', $id)
                        ->value('pontos') ?? 0);
    }
}
