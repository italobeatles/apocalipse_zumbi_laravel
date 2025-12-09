<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResetPasswordController extends Controller {

    protected string $redirectTo = '/home';

    public function unavailable(): JsonResponse {
        return response()->json([
                    'message' => 'Password reset endpoints are not configured in this API.',
                ], Response::HTTP_NOT_IMPLEMENTED);
    }
}

