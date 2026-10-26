<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiExceptionRenderer
{
    public function register(Exceptions $exceptions): void
    {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson());

        $exceptions->render(fn (DeletionBlockedException $e, Request $request) => $this->respond($request, $e->getMessage(), 409));
        $exceptions->render(fn (DuplicateEntryException $e, Request $request) => $this->respond($request, $e->getMessage(), 409));
        $exceptions->render(fn (MissingMilestoneColumnException $e, Request $request) => $this->respond($request, $e->getMessage(), 409));
        $exceptions->render(fn (AuthenticationException $e, Request $request) => $this->respond($request, 'Sessão expirada. Faça login novamente.', 401));
        $exceptions->render(fn (AuthorizationException $e, Request $request) => $this->respond($request, $e->getMessage() ?: 'Você não tem permissão para executar esta ação.', 403));
        $exceptions->render(fn (ModelNotFoundException $e, Request $request) => $this->respond($request, 'O registro solicitado não foi encontrado.', 404));
        $exceptions->render(fn (NotFoundHttpException $e, Request $request) => $this->respond($request, 'O recurso solicitado não existe.', 404));
    }

    private function respond(Request $request, string $message, int $status): ?JsonResponse
    {
        if (! $request->is('api/*')) {
            return null;
        }

        return response()->json(['message' => $message], $status);
    }
}
