<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use App\Http\Requests\PaginatedListRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\Admin\ClientService;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function __construct(private ClientService $clients) {}

    public function index(PaginatedListRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Client::class);

        return ClientResource::collection($this->clients->paginate($request->perPage()))->response();
    }

    public function store(ClientRequest $request): JsonResponse
    {
        $this->authorize('create', Client::class);

        $client = $this->clients->create($request->validated());

        return ClientResource::make($client)
            ->additional(['message' => "Cliente \"{$client->razao_social}\" criado."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(ClientRequest $request, Client $client): JsonResponse
    {
        $this->authorize('update', $client);

        $client = $this->clients->update($client, $request->validated());

        return ClientResource::make($client)
            ->additional(['message' => "Cliente \"{$client->razao_social}\" atualizado."])
            ->response();
    }

    public function destroy(Client $client): JsonResponse
    {
        $this->authorize('delete', $client);

        $this->clients->delete($client);

        return response()->json(['message' => "Cliente \"{$client->razao_social}\" excluído."]);
    }
}
