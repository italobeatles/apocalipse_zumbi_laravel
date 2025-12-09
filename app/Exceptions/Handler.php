<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Registre callbacks de tratamento.
     */
    public function register(): void
    {
        // Exemplos:
        // $this->reportable(function (Throwable $e) {});
        // $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {});
    }

    // Se você PRECISAR sobrescrever, mantenha as assinaturas modernas:
    // public function report(Throwable $e): void
    // {
    //     parent::report($e);
    // }

    // public function render($request, Throwable $e): \Symfony\Component\HttpFoundation\Response
    // {
    //     return parent::render($request, $e);
    // }
}
