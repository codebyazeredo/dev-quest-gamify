<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use App\Services\GithubPullRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GithubWebhookController extends Controller
{
    public function __construct(private GithubPullRequestService $pullRequests) {}

    public function __invoke(Request $request): JsonResponse
    {
        $secret = config('services.github.webhook_secret');

        if (! $secret) {
            return response()->json(['message' => 'O segredo do webhook do GitHub não está configurado no servidor.'], 503);
        }

        $expectedSignature = 'sha256='.hash_hmac('sha256', $request->getContent(), $secret);

        if (! hash_equals($expectedSignature, (string) $request->header('X-Hub-Signature-256'))) {
            return response()->json(['message' => 'A assinatura do webhook do GitHub é inválida.'], 401);
        }

        $event = (string) $request->header('X-GitHub-Event');

        if ($event === 'ping') {
            return response()->json(['message' => 'Webhook do GitHub configurado com sucesso.']);
        }

        if ($event !== 'pull_request') {
            return response()->json(['message' => "O evento \"{$event}\" do GitHub não é tratado e foi ignorado."], 202);
        }

        $payload = $request->json()->all();

        $validator = Validator::make($payload, [
            'repository.full_name' => ['required', 'string'],
            'pull_request.html_url' => ['required', 'string'],
            'pull_request.head.ref' => ['required', 'string'],
            'pull_request.state' => ['required', 'in:open,closed'],
            'pull_request.merged' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'O payload do pull request está fora do formato esperado.', 'errors' => $validator->errors()], 422);
        }

        return response()->json(['message' => $this->pullRequests->sync($payload)]);
    }
}
