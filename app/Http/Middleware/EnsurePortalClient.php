<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalClient
{
    public const SESSION_KEY = 'portal_client_id';

    public function handle(Request $request, Closure $next): Response
    {
        $client = Client::query()->where('is_active', true)->find($request->session()->get(self::SESSION_KEY));

        if ($client === null) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('portal.login');
        }

        $request->attributes->set('portal_client', $client);

        return $next($request);
    }
}
