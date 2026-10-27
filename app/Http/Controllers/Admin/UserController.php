<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Requests\PaginatedListRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private UserService $users) {}

    public function index(PaginatedListRequest $request): JsonResponse
    {
        $this->authorize('accessAdminPanel', User::class);

        return UserResource::collection($this->users->paginate($request->perPage()))
            ->additional(['form_options' => $this->formOptions()])
            ->response();
    }

    public function store(UserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();
        $user = $this->users->create($validated['person_id'], $validated['email'], $validated['password'], $validated['roles']);

        return UserResource::make($user)
            ->additional(['message' => "Usuário \"{$user->name}\" criado."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validated();
        $user = $this->users->update($user, $validated['email'], $validated['password'] ?? null, $validated['roles']);

        return UserResource::make($user)
            ->additional(['message' => "Usuário \"{$user->name}\" atualizado."])
            ->response();
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $this->users->delete($user);

        return response()->json(['message' => "Usuário \"{$user->name}\" excluído."]);
    }

    private function formOptions(): array
    {
        $options = $this->users->formOptions();

        return [
            'people' => $options['people']->map(fn ($person) => ['id' => $person->id, 'label' => "{$person->nome} ({$person->cpf})"])->values(),
            'roles' => $options['roles']->map(fn (string $name) => ['name' => $name, 'label' => UserRole::labelFor($name)])->values(),
        ];
    }
}
