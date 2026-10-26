<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Http\Requests\PaginatedListRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\User;
use App\Services\Admin\RoleService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(private RoleService $roles) {}

    public function index(PaginatedListRequest $request): JsonResponse
    {
        $this->authorize('accessAdminPanel', User::class);

        return RoleResource::collection($this->roles->paginate($request->perPage()))
            ->additional(['permissions' => PermissionResource::collection($this->roles->permissions())])
            ->response();
    }

    public function store(RoleRequest $request): JsonResponse
    {
        $this->authorize('accessAdminPanel', User::class);

        $role = $this->roles->create($request->validated('name'));

        return RoleResource::make($role)
            ->additional(['message' => "Role \"{$role->name}\" criado."])
            ->response()
            ->setStatusCode(201);
    }

    public function togglePermission(Role $role, int $permission): JsonResponse
    {
        $this->authorize('accessAdminPanel', User::class);

        return RoleResource::make($this->roles->togglePermission($role, $permission))->response();
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->authorize('accessAdminPanel', User::class);

        $this->roles->delete($role);

        return response()->json(['message' => "Role \"{$role->name}\" excluído."]);
    }
}
