<?php

namespace App\Http\Middleware;

use App\Models\Loket;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticate
{
    /**
     * Memvalidasi API token petugas loket.
     *
     * Token diterima lewat header "Authorization: Bearer <token>",
     * parameter query "api_token", atau body JSON "api_token".
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken()
            ?? $request->query('api_token')
            ?? $request->input('api_token');

        $user = $token ? User::where('api_token', $token)->first() : null;

        if (! $user || ! $user->isLoket()) {
            return $this->unauthorized('Token tidak valid atau tidak memiliki akses loket.');
        }

        if ($user->canAccessAllClusters()) {
            $loket = Loket::query()
                ->where('is_active', true)
                ->whereNotNull('poli_id')
                ->when($request->header('X-Loket-Id') ?? $request->input('loket_id'), function ($query, $loketId) {
                    $query->whereKey($loketId);
                })
                ->with('poli')
                ->first();
        } else {
            $loket = $user->loket;
        }

        if (! $loket || ! $loket->is_active || ! $loket->poli_id) {
            return $this->unauthorized('Loket tidak terkonfigurasi atau tidak aktif.');
        }

        $request->attributes->set('api_user', $user);
        $request->attributes->set('api_loket', $loket);

        return $next($request);
    }

    private function unauthorized(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 401);
    }
}
