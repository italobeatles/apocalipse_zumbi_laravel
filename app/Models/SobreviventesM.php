<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Throwable;

class SobreviventesM extends Model {

    protected $table = 'tbsobreviventes';
    protected $fillable = ['nome', 'sexo', 'idade', 'latitude', 'longitude', 'zumbi'];
    public $timestamps = false;
    protected $casts = [
        'idade' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'zumbi' => 'boolean',
    ];

    /**
     * Remove um sobrevivente e seus recursos.
     */
    public function removerSobrevivente(int $id): bool {
        return DB::transaction(function () use ($id) {
                    DB::table('tbsobreviventes_recursos')
                            ->where('id_sobrevivente', $id)
                            ->delete();

                    $deleted = DB::table('tbsobreviventes')
                            ->where('id', $id)
                            ->delete();

                    return $deleted > 0;
                });
    }

    /**
     * Registra uma notificação de contaminação e marca como zumbi a partir de 3 avisos.
     */
    public function informarContaminacao(int $id_informante, int $id_sobrevivente): void {
        DB::table('tbavisos_zumbificacao')->insert([
            'id_sobrevivente' => $id_sobrevivente,
            'id_informante' => $id_informante,
        ]);

        $total = $this->verificarExisteNotificacaoContaminacaoTotal($id_sobrevivente);

        if ($total >= 3) {
            DB::table('tbsobreviventes')
                    ->where('id', $id_sobrevivente)
                    ->update(['zumbi' => 1]);
        }
    }

    /**
     * Situação do sobrevivente (0/1).
     */
    public function informarSituacaoSobrevivente(int $id_sobrevivente): int {
        return (int) DB::table('tbsobreviventes')
                        ->where('id', $id_sobrevivente)
                        ->value('zumbi');
    }

    /**
     * Quantas notificações esse informante fez contra esse sobrevivente.
     */
    public function verificarExisteNotificacaoContaminacao(int $id_informante, int $id_sobrevivente): int {
        return DB::table('tbavisos_zumbificacao')
                        ->where('id_sobrevivente', $id_sobrevivente)
                        ->where('id_informante', $id_informante)
                        ->count();
    }

    /**
     * Total de notificações que um sobrevivente sofreu.
     */
    public function verificarExisteNotificacaoContaminacaoTotal(int $id_sobrevivente): int {
        return DB::table('tbavisos_zumbificacao')
                        ->where('id_sobrevivente', $id_sobrevivente)
                        ->count();
    }

    /**
     * Lista sobreviventes não-zumbis (todos ou por id).
     */
    public function retornarSobreviventes(?int $id = null): Collection {
        $query = $this->newQuery()->where('zumbi', 0);

        if ($id !== null) {
            $query->where('id', $id);
        }

        return $query->get();
    }

    /**
     * Relatório de infectados vs não infectados.
     */
    public function retornarRelatorioInfectados(): array {
        $infectados = DB::table('tbsobreviventes')->where('zumbi', 1)->count();
        $naoInfectados = DB::table('tbsobreviventes')->where('zumbi', 0)->count();

        return [
            'infectados' => $infectados,
            'nao_infectados' => $naoInfectados,
        ];
    }
}
