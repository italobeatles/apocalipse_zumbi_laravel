<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecursosM;
use App\Models\SobreviventesM;
use App\Models\SobreviventesRecursosM;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InventarioSobreviventesC extends Controller {

    /**
     * GET /api/inventario
     * Retorna sobreviventes não-zumbis com seus recursos.
     */
    public function index(): JsonResponse {
        $sobreviventesRepo = new SobreviventesM();
        $recursosRepo = new SobreviventesRecursosM();

        $lista = $sobreviventesRepo->retornarSobreviventes(); // só não-zumbis
        $out = [];

        foreach ($lista as $row) {
            // $row pode ser Model/Arrayable; garantir objeto simples:
            $attrs = is_array($row) ? $row['attributes'] ?? $row : ($row->toArray());
            $obj = (object) $attrs;
            $obj->recursos = $recursosRepo->retornarRecursosSobrevivente((int) $obj->id);
            $out[] = $obj;
        }

        return response()->json($out, Response::HTTP_OK);
    }

    /**
     * GET /api/inventario/{id}
     * Retorna um sobrevivente não-zumbi + seus recursos.
     */

    /**
     * Display the specified resource. * * 
     * @param int $id
     * @return \Illuminate\Http\Response 
     */
    public function show(int $id): JsonResponse {
        $sobreviventesRepo = new SobreviventesM();
        $recursosRepo = new SobreviventesRecursosM();

        $lista = $sobreviventesRepo->retornarSobreviventes($id);
        if ($lista->isEmpty()) {
            return response()->json(['message' => 'Sobrevivente não encontrado ou é zumbi'], Response::HTTP_NOT_FOUND);
        }

        $row = $lista->first();
        $attrs = is_array($row) ? $row['attributes'] ?? $row : ($row->toArray());
        $obj = (object) $attrs;
        $obj->recursos = $recursosRepo->retornarRecursosSobrevivente((int) $obj->id);

        return response()->json($obj, Response::HTTP_OK);
    }

    /**
     * POST /api/inventario/troca
     * Realiza troca de recursos entre dois sobreviventes.
     *
     * Espera:
     * - id_sobrevivente: [A, B]
     * - recursos_troca:  [
     *     [ {id_recurso:int, quantidade:int}, ... ],   // itens que A entrega
     *     [ {id_recurso:int, quantidade:int}, ... ],   // itens que B entrega
     *   ]
     * Aceita também recursos_troca como *JSON string* (legado) para cada lado.
     */
// App/Http/Controllers/Api/InventarioSobreviventesC.php

    public function store(Request $request) {
        if (sizeof($request->id_sobrevivente) != 2 || sizeof($request->recursos_troca) != 2) {
            return response()->json([
                        'status' => 'falha',
                        'mensagem' => 'pra realizar uma troca, são necessários apenas 2 sobreviventes com duas cargas de recursos para a troca!'
                            ], 422);
        }

        foreach ($request->id_sobrevivente as $key => $id) {
            if ($this->verificarZumbi($id)) {
                return response()->json([
                            'status' => 'falha',
                            'mensagem' => 'Pelo menos um dos dois envolvidos na troca está contaminado!'
                                ], 422);
            }
            if ($this->verificarQuantidadeRecursos($id, $request->recursos_troca[$key]) == false) {
                return response()->json([
                            'status' => 'falha',
                            'mensagem' => 'Um dos sobreviventes não possui os recursos informados na troca!'
                                ], 422);
            }
        }

        if (!$this->verificarPontuacaoRecursos($request->recursos_troca)) {
            return response()->json([
                        'status' => 'falha',
                        'mensagem' => 'A quantidade de pontos dos recursos oferecidos para a troca não são iguais!'
                            ], 422);
        }

        if (!$this->realizarTroca($request)) {
            return response()->json([
                        'status' => 'falha',
                        'mensagem' => 'Erro interno na requisição!'
                            ], 500);
        }

        return response()->json(['status' => 'sucesso', 'mensagem' => 'Troca efetivada com sucesso!'], 200);
    }

    /**
     * Verificando se o indivíduo informado está contaminado * * 
     * @param int $id
     * @return bool 
     */
    private function verificarZumbi(int $id): bool {
        $obj = new SobreviventesM();
        return $obj->informarSituacaoSobrevivente($id) == 1 ? true : false;
    }

    /**
     * Verificando se há quantidade de recursos disponíveis para a troca * 
     * @param int $id  
     * @param mixed $recursos 
     * @return bool 
     */
    private function verificarQuantidadeRecursos(int $id, mixed $recursos): bool {
        if (is_string($recursos)) {
            $arr = json_decode($recursos);
        } else {
            // array PHP: pode ser array de arrays ou de stdClass
            $arr = $recursos;
        }

        $obj = new SobreviventesRecursosM();

        foreach ($arr as $data) {
            // Normaliza para objeto
            if (is_array($data)) {
                $data = (object) $data;
            }

            if ((int) $data->quantidade > $obj->retornarQuantidadeRecursos(
                            (int) $id,
                            (int) $data->id_recurso
                    )) {
                return false;
            }
        }

        return true;
    }

    /*
     * Verificando se há igualdade na pontuação de recursos para a troca * * 
     * @param array $recursos 
     * @return bool 
     */

    private function verificarPontuacaoRecursos(array $recursos): bool {
        $obj = new RecursosM();
        $varPontuacao = array(0 => 0, 1 => 0);
        foreach ($recursos AS $key => $r) {
            $arr = is_string($r) ? json_decode($r, true) : $r;
            if (!is_array($arr)) {
                return false;
            }

            foreach ($arr as $data) {
                $data = is_object($data) ? (array) $data : $data;
                $varPontuacao[$key] += (int) $data['quantidade'] * $obj->retornarQuantidadePontos((int) $data['id_recurso']);
            }
        } return ($varPontuacao[0] == $varPontuacao[1]) ? true : false;
    }

    /**
     * Display the specified resource. * * 
     * @param Request $request * 
     * @return bool 
     */
    private function realizarTroca(Request $request): bool {
        $obj = new SobreviventesRecursosM();
        $id_sobrevivente = array($request->id_sobrevivente[0], $request->id_sobrevivente[1]);
        $normalizar = function ($entrada) {
            $arr = is_string($entrada) ? json_decode($entrada) : $entrada;
            $arr = is_array($arr) ? $arr : [];

            return array_map(function ($item) {
                return is_array($item) ? (object) $item : $item;
            }, $arr);
        };

        $recursos_troca = [
            $normalizar($request->recursos_troca[0]),
            $normalizar($request->recursos_troca[1]),
        ];
        return $obj->realizarTroca($id_sobrevivente, $recursos_troca);
    }
}
