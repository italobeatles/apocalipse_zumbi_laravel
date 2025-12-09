<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Apocalipse Zumbi API",
 *     version="1.0.0",
 *     description="Documentação Swagger gerada pelo L5-Swagger"
 * )
 */
class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
