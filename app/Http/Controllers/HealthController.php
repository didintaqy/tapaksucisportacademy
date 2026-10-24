<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Endpoint sederhana untuk monitoring uptime dan kesiapan aplikasi.
     */
    public function __invoke(): JsonResponse
    {
        try {
            DB::connection()->getPdo();
            Cache::put('health_check', true, 10);

            return response()->json([
                'status' => 'ok',
                'database' => 'ok',
                'cache' => 'ok',
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aplikasi tidak siap melayani request.',
                'timestamp' => now()->toIso8601String(),
            ], 503);
        }
    }
}
