<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Auth scaffolding was removed in Laravel 12. Keep controller as a stub
 * to avoid breaking existing references and to document the missing feature.
 */
class LoginController extends Controller {

    protected string $redirectTo = '/home';

    public function unavailable(): JsonResponse {
        return response()->json([
                    'message' => 'Login endpoints are not configured in this API.',
                ], Response::HTTP_NOT_IMPLEMENTED);
    }
}

