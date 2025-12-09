<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SobreviventesM;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class InformarZumbificacaoC extends Controller {

    /**
     * Opcional: bloquear listagem nesse endpoint.
     */
    public function index(): JsonResponse {
        return response()->json(['message' => 'Method Not Allowed'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * POST /api/informar-zumbificacao
     */
    public function store(Request $request): JsonResponse {
        // Validação mínima de entrada
        $data = $request->validate([
            'id_informante' => ['required', 'integer'],
            'id_sobrevivente' => ['required', 'integer', 'different:id_informante'],
        ]);

        try {
            $repo = new SobreviventesM();

            // Regras de negócio originais com HTTP codes apropriados
            if ($repo->informarSituacaoSobrevivente($data['id_informante']) === 1) {
                return response()->json([
                            'status' => 'Informante contaminado! Como um zumbi poderia informar a contaminação de um sobrevivente?',
                            'code' => 'INFORMANTE_ZUMBI',
                                ], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
            }

            if ($repo->informarSituacaoSobrevivente($data['id_sobrevivente']) === 1) {
                return response()->json([
                            'status' => 'Este sobrevivente já foi marcado como contaminado!',
                            'code' => 'SOBREVIVENTE_JA_CONTAMINADO',
                                ], Response::HTTP_CONFLICT); // 409
            }

            if ($repo->verificarExisteNotificacaoContaminacao($data['id_informante'], $data['id_sobrevivente']) > 0) {
                return response()->json([
                            'status' => 'Este informante já "deu o aviso" de contaminação!',
                            'code' => 'AVISO_DUPLICADO',
                                ], Response::HTTP_CONFLICT); // 409
            }

            // Efeito da ação
            $repo->informarContaminacao($data['id_informante'], $data['id_sobrevivente']);

            return response()->json([
                        'status' => 'Obrigado pela informação de contaminação! Tenha cuidado!',
                        'code' => 'AVISO_REGISTRADO',
                            ], Response::HTTP_CREATED); // 201
        } catch (Throwable $e) {
            // Última linha de defesa
            return response()->json([
                        'status' => 'ERROR',
                        'message' => $e->getMessage(),
                            ], Response::HTTP_INTERNAL_SERVER_ERROR); // 500
        }
    }
}
