<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BusinessSectorRequest;
use App\Http\Requests\PaginatedListRequest;
use App\Http\Resources\BusinessSectorResource;
use App\Models\BusinessSector;
use App\Services\Admin\BusinessSectorService;
use Illuminate\Http\JsonResponse;

class BusinessSectorController extends Controller
{
    public function __construct(private BusinessSectorService $sectors) {}

    public function index(PaginatedListRequest $request): JsonResponse
    {
        $this->authorize('viewAny', BusinessSector::class);

        return BusinessSectorResource::collection($this->sectors->paginate($request->perPage()))->response();
    }

    public function store(BusinessSectorRequest $request): JsonResponse
    {
        $this->authorize('create', BusinessSector::class);

        $sector = $this->sectors->create($request->validated());

        return BusinessSectorResource::make($sector)
            ->additional(['message' => "Ramo \"{$sector->name}\" criado."])
            ->response()
            ->setStatusCode(201);
    }

    public function update(BusinessSectorRequest $request, BusinessSector $sector): JsonResponse
    {
        $this->authorize('update', $sector);

        $sector = $this->sectors->update($sector, $request->validated());

        return BusinessSectorResource::make($sector)
            ->additional(['message' => "Ramo \"{$sector->name}\" atualizado."])
            ->response();
    }

    public function destroy(BusinessSector $sector): JsonResponse
    {
        $this->authorize('delete', $sector);

        $this->sectors->delete($sector);

        return response()->json(['message' => "Ramo \"{$sector->name}\" excluído."]);
    }
}
