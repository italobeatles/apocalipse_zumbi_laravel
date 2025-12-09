<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ForgotPasswordController extends Controller {

    public function unavailable(): JsonResponse {
        return response()->json([
                    'message' => 'Password reset endpoints are not configured in this API.',
                ], Response::HTTP_NOT_IMPLEMENTED);
    }
}
