<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
   public function check(): JsonResponse
    {   
        try {
            DB::connection()->getPdo();


            return response()->json([
                'status' => 'ok',
                'message' => 'MySQL connection is alive',
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'MySQL connection failed',
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }

}
