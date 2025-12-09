<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * Anotações OpenAPI para documentar a API com L5-Swagger.
 */
class DocsController extends Controller {

    /**
     * @OA\Get(
     *     path="/api/sobreviventes",
     *     tags={"Sobreviventes"},
     *     summary="Lista todos os sobreviventes",
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function sobreviventesIndex(): void {
    }

    /**
     * @OA\Post(
     *     path="/api/sobreviventes",
     *     tags={"Sobreviventes"},
     *     summary="Cria um sobrevivente com inventario",
     *     @OA\Response(response=201, description="Criado"),
     *     @OA\Response(response=422, description="Dados invalidos")
     * )
     */
    public function sobreviventesStore(): void {
    }

    /**
     * @OA\Post(
     *     path="/api/informar-zumbificacao",
     *     tags={"Zumbificacao"},
     *     summary="Registra aviso de contaminacao",
     *     @OA\Response(response=201, description="Aviso registrado"),
     *     @OA\Response(response=409, description="Duplicado ou ja contaminado"),
     *     @OA\Response(response=422, description="Informante zumbi")
     * )
     */
    public function informarZumbificacao(): void {
    }

    /**
     * @OA\Post(
     *     path="/api/inventario/troca",
     *     tags={"Inventario"},
     *     summary="Realiza troca de recursos entre dois sobreviventes",
     *     @OA\Response(response=200, description="Troca efetuada"),
     *     @OA\Response(response=422, description="Pontuacao ou estoque insuficiente"),
     *     @OA\Response(response=500, description="Erro interno")
     * )
     */
    public function troca(): void {
    }

    /**
     * @OA\Get(
     *     path="/api/relatorio-geral",
     *     tags={"Relatorios"},
     *     summary="Resumo de infectados e inventario",
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function relatorio(): void {
    }
}

