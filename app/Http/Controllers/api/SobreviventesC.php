<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SobreviventesM;
use App\Models\SobreviventesRecursosM;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class SobreviventesC extends Controller {

    /**
     * GET /api/sobreviventes
     */
    public function index(): JsonResponse {
        return response()->json(SobreviventesM::all(), Response::HTTP_OK);
    }

    /**
     * POST /api/sobreviventes
     */
    public function store(Request $request): JsonResponse {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'idade' => ['required', 'integer', 'min:0'],
            'sexo' => ['required', 'string', 'in:M,F,O'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'zumbi' => ['required', 'boolean'],
            'id_recurso' => ['array'],
            'id_recurso.*' => ['integer'],
            'quantidade' => ['array'],
            'quantidade.*' => ['integer', 'min:0'],
        ]);

        try {
            $sobrevivente = SobreviventesM::create([
                'nome' => $data['nome'],
                'idade' => $data['idade'],
                'sexo' => $data['sexo'],
                'latitude' => (float) $data['latitude'],
                'longitude' => (float) $data['longitude'],
                'zumbi' => (bool) $data['zumbi'],
            ]);

            if (!empty($data['id_recurso'] ?? null)) {
                foreach ($data['id_recurso'] as $idx => $recursoId) {
                    SobreviventesRecursosM::create([
                        'id_sobrevivente' => $sobrevivente->id,
                        'id_recurso' => $recursoId,
                        'quantidade' => (int) ($data['quantidade'][$idx] ?? 0),
                    ]);
                }
            }

            return response()->json(['status' => 'OK', 'id' => $sobrevivente->id], Response::HTTP_CREATED);
        } catch (Throwable $e) {
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/sobreviventes/{id}
     */
    public function show(SobreviventesM $sobrevivente): JsonResponse {
        return response()->json($sobrevivente, Response::HTTP_OK);
    }

    /**
     * PUT/PATCH /api/sobreviventes/{id}
     */
    public function update(Request $request, SobreviventesM $sobrevivente): JsonResponse {
        try {
            // regra de negócio original: zumbi não atualiza
            if (method_exists($sobrevivente, 'informarSituacaoSobrevivente') && $sobrevivente->informarSituacaoSobrevivente($sobrevivente->id) === 1) {
                return response()->json(['status' => 'Zumbi não pode informar localização!'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data = $request->validate([
                'nome' => ['sometimes', 'string', 'max:255'],
                'idade' => ['sometimes', 'integer', 'min:0'],
                'sexo' => ['sometimes', 'string', 'in:M,F,O'],
                'latitude' => ['sometimes', 'numeric'],
                'longitude' => ['sometimes', 'numeric'],
                'zumbi' => ['sometimes', 'boolean'],
            ]);

            $sobrevivente->update($data);

            return response()->json(['status' => 'OK'], Response::HTTP_OK);
        } catch (Throwable $e) {
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DELETE /api/sobreviventes/{id}
     */
    public function destroy(SobreviventesM $sobrevivente): JsonResponse {
        try {
            if (method_exists($sobrevivente, 'removerSobrevivente')) {
                $sobrevivente->removerSobrevivente((int) $sobrevivente->id);
            } else {
                $sobrevivente->delete();
            }

            return response()->json(['status' => 'OK'], Response::HTTP_OK);
        } catch (Throwable $e) {
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * POST /api/contaminacao
     */
    public function informarContaminacao(Request $request): JsonResponse {
        $data = $request->validate([
            'id_informante' => ['required', 'integer'],
            'id_sobrevivente' => ['required', 'integer', 'different:id_informante'],
        ]);

        $obj = new SobreviventesM();

        if ($obj->informarSituacaoSobrevivente($data['id_informante']) === true) {
            return response()->json(['status' => 'Informante contaminado! Como um zumbi poderia informar a contaminação de um sobrevivente?'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($obj->informarSituacaoSobrevivente($data['id_sobrevivente']) === true) {
            return response()->json(['status' => 'Este sobrevivente já foi marcado como contaminado!'], Response::HTTP_CONFLICT);
        }

        if ($obj->verificarExisteNotificacaoContaminacao($data['id_informante'], $data['id_sobrevivente']) > 0) {
            return response()->json(['status' => 'Este informante já "deu o aviso" de contaminação!'], Response::HTTP_CONFLICT);
        }

        $obj->informarContaminacao($data['id_informante'], $data['id_sobrevivente']);

        return response()->json(['status' => 'Obrigado pela informação de contaminação! Tenha cuidado!'], Response::HTTP_CREATED);
    }
}
