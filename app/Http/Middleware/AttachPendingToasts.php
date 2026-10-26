<?php

namespace App\Http\Middleware;

use App\Support\ToastCollector;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AttachPendingToasts
{
    public function __construct(private ToastCollector $toasts) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response instanceof JsonResponse) {
            return $response;
        }

        $toasts = $this->toasts->flush();

        if ($toasts !== []) {
            $response->setData([...(array) $response->getData(true), 'toasts' => $toasts]);
        }

        return $response;
    }
}
