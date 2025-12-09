<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SobreviventesRecursosM;
use App\Models\SobreviventesM;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RelatorioGeralC extends Controller {

    /**
     * GET /api/relatorio-geral
     */
    public function index(): JsonResponse {
        $sr = new SobreviventesRecursosM();

        $json = [
            'disponibilidade_recursos' => $this->organizarRelatorioRecursos($sr->retornarRelatorioRecursos()),
            'balanco_infectados' => $this->organizarRelatorioInfectados(),
        ];

        return response()->json($json, Response::HTTP_OK);
    }

    /**
     * Monta:
     * - disponibilidade_de_recursos_por_pessoa: [nome => [ {recurso, quantidade, porcentagem}, ... ]]
     * - media_de_recursos_por_pessoa: [recurso => média]
     *
     * @param array $rs (linhas com ->sobrevivente, ->recurso, ->quantidade, ->porcentagem)
     */
    private function organizarRelatorioRecursos(array $rs): array {
        $porPessoa = [];
        $totaisPorRecurso = [];

        foreach ($rs as $row) {
            $data = (array) $row;
            $sobrevivente = $data['sobrevivente'] ?? '';
            $recurso = $data['recurso'] ?? '';
            $quantidade = (int) ($data['quantidade'] ?? 0);
            $porcentagem = (float) ($data['porcentagem'] ?? 0);

            // agrupa por sobrevivente
            $porPessoa[$sobrevivente][] = [
                'recurso' => $recurso,
                'quantidade' => $quantidade,
                'porcentagem' => $porcentagem,
            ];

            // soma total por recurso
            $totaisPorRecurso[$recurso] = ($totaisPorRecurso[$recurso] ?? 0) + $quantidade;
        }

        $qtdPessoas = max(1, count($porPessoa)); // evita divisão por zero
        $mediaPorRecurso = [];
        foreach ($totaisPorRecurso as $recurso => $totalQtd) {
            $mediaPorRecurso[$recurso] = $totalQtd / $qtdPessoas;
        }

        return [
            'disponibilidade_de_recursos_por_pessoa' => $porPessoa,
            'media_de_recursos_por_pessoa' => $mediaPorRecurso,
        ];
    }

    /**
     * Retorna:
     * - total
     * - infectados_porcentagem
     * - nao_infectados_porcentagem
     */
    private function organizarRelatorioInfectados(): array {
        $m = new SobreviventesM();
        $row = $m->retornarRelatorioInfectados();

        $infectados = (int) ($row['infectados'] ?? 0);
        $naoInfectados = (int) ($row['nao_infectados'] ?? 0);
        $total = $infectados + $naoInfectados;

        if ($total === 0) {
            return [
                'total' => 0,
                'infectados_porcentagem' => 0.0,
                'nao_infectados_porcentagem' => 0.0,
            ];
        }

        // arredonda com 2 casas (opcional)
        $pctInf = round($infectados * 100 / $total, 2);
        $pctNao = round($naoInfectados * 100 / $total, 2);

        return [
            'total' => $total,
            'infectados_porcentagem' => $pctInf,
            'nao_infectados_porcentagem' => $pctNao,
        ];
    }
}
