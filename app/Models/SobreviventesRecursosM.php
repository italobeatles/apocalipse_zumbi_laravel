<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Throwable;

class SobreviventesRecursosM extends Model {

    protected $table = 'tbsobreviventes_recursos';
    protected $fillable = ['id_recurso', 'id_sobrevivente', 'quantidade'];
    public $timestamps = false;
    protected $casts = [
        'id_recurso' => 'integer',
        'id_sobrevivente' => 'integer',
        'quantidade' => 'integer',
    ];

    /**
     * Recursos de um sobrevivente (nome do recurso + quantidade)
     */
    public function retornarRecursosSobrevivente(int $id_sobrevivente): array {
        return DB::table($this->table . ' as sr')
                        ->join('tbrecursos as r', 'sr.id_recurso', '=', 'r.id')
                        ->where('sr.id_sobrevivente', $id_sobrevivente)
                        ->orderBy('r.descricao')
                        ->get(['r.descricao as recurso', 'sr.quantidade'])
                        ->map(fn($row) => (object) [
                            'recurso' => (string) $row->recurso,
                            'quantidade' => (int) $row->quantidade,
                        ])
                        ->all();
    }

    /**
     * Quantidade de um recurso específico para um sobrevivente.
     */
    public function retornarQuantidadeRecursos(int $id_sobrevivente, int $id_recurso): int {
        return (int) DB::table($this->table)
                        ->where('id_sobrevivente', $id_sobrevivente)
                        ->where('id_recurso', $id_recurso)
                        ->value('quantidade') ?? 0;
    }

    /**
     * Troca de recursos entre DOIS sobreviventes.
     * Exige arrays com objetos {id_recurso, quantidade} para cada lado.
     *
     * @param array $id_sobrevivente [A, B]
     * @param array $recursos_troca  [ [obj...], [obj...] ]
     */
    public function realizarTroca(array $id_sobrevivente, array $recursos_troca): bool {
        // Estrutura esperada
        // $id_sobrevivente = [ int $ida, int $idb ]
        // $recursos_troca  = [
        //   [ (object){id_recurso:int, quantidade:int}, ... ], // recursos que A entrega
        //   [ (object){id_recurso:int, quantidade:int}, ... ], // recursos que B entrega
        // ]

        return DB::transaction(function () use ($id_sobrevivente, $recursos_troca) {
                    [$ida, $idb] = $id_sobrevivente;

                    // 1) Validar que ambos têm estoque suficiente
                    $this->assertEstoqueSuficiente($ida, $recursos_troca[0]);
                    $this->assertEstoqueSuficiente($idb, $recursos_troca[1]);

                    // 2) Debitar
                    $this->alterarInventario($ida, $recursos_troca[0], '-');
                    $this->alterarInventario($idb, $recursos_troca[1], '-');

                    // 3) Creditar (se não existir a linha, cria com quantidade 0 e soma)
                    $this->alterarInventario($ida, $recursos_troca[1], '+', upsert: true);
                    $this->alterarInventario($idb, $recursos_troca[0], '+', upsert: true);

                    return true;
                });
    }

    /**
     * Verifica se o sobrevivente possui estoque suficiente para todos os itens.
     * Lança \RuntimeException em caso de falta.
     *
     * @param array $itens array de objetos {id_recurso, quantidade}
     */
    private function assertEstoqueSuficiente(int $id_sobrevivente, array $itens): void {
        foreach ($itens as $item) {
            $recursoId = (int) $item->id_recurso;
            $qtd = (int) $item->quantidade;

            if ($qtd <= 0) {
                throw new \RuntimeException("Quantidade inválida para recurso {$recursoId}");
            }

            $atual = $this->retornarQuantidadeRecursos($id_sobrevivente, $recursoId);
            if ($atual < $qtd) {
                throw new \RuntimeException("Estoque insuficiente do recurso {$recursoId} para o sobrevivente {$id_sobrevivente}");
            }
        }
    }

    /**
     * Aplica variação de inventário.
     *
     * @param array $itens array de objetos {id_recurso, quantidade}
     * @param string $op   '+' ou '-'
     * @param bool $upsert cria linha se não existir (apenas para '+')
     */
    private function alterarInventario(int $id_sobrevivente, array $itens, string $op = '+', bool $upsert = false): void {
        foreach ($itens as $item) {
            $recursoId = (int) $item->id_recurso;
            $qtd = (int) $item->quantidade;

            if ($upsert) {
                $exists = DB::table($this->table)
                        ->where('id_sobrevivente', $id_sobrevivente)
                        ->where('id_recurso', $recursoId)
                        ->exists();

                if (!$exists) {
                    DB::table($this->table)->insert([
                        'id_sobrevivente' => $id_sobrevivente,
                        'id_recurso' => $recursoId,
                        'quantidade' => 0,
                    ]);
                }
            }

            $delta = $op === '+' ? $qtd : -$qtd;
            $updated = DB::table($this->table)
                    ->where('id_sobrevivente', $id_sobrevivente)
                    ->where('id_recurso', $recursoId)
                    ->update([
                        'quantidade' => DB::raw("CASE WHEN quantidade + {$delta} < 0 THEN 0 ELSE quantidade + {$delta} END")
            ]);

            if ($updated === 0 && !$upsert) {
                throw new \RuntimeException("Par (sobrevivente, recurso) inexistente: ({$id_sobrevivente}, {$recursoId})");
            }
        }
    }

    /**
     * Relatório de recursos: sobrevivente, recurso, quantidade e % por recurso.
     */
    public function retornarRelatorioRecursos(): array {
        // Usamos bindings e Query Builder p/ evitar SQL injection.
        return DB::table('tbsobreviventes_recursos as sr')
                        ->join('tbsobreviventes as s', 'sr.id_sobrevivente', '=', 's.id')
                        ->join('tbrecursos as r', 'sr.id_recurso', '=', 'r.id')
                        ->where('s.zumbi', 0)
                        ->orderBy('s.nome')
                        ->orderBy('r.id')
                        ->get([
                            's.nome as sobrevivente',
                            'r.descricao as recurso',
                            'sr.quantidade',
                            DB::raw('sr.quantidade * 100.0 / NULLIF((SELECT SUM(x.quantidade) FROM tbsobreviventes_recursos x WHERE x.id_recurso = r.id), 0) as porcentagem')
                        ])
                        ->map(fn($row) => (object) [
                            'sobrevivente' => $row->sobrevivente,
                            'recurso' => $row->recurso,
                            'quantidade' => (int) $row->quantidade,
                            'porcentagem' => (float) $row->porcentagem,
                        ])
                        ->all();
    }
}
