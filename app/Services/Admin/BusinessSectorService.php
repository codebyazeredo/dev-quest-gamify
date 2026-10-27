<?php

namespace App\Services\Admin;

use App\Exceptions\DeletionBlockedException;
use App\Models\BusinessSector;
use App\Repositories\BusinessSectorRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BusinessSectorService
{
    public function __construct(private BusinessSectorRepository $sectors) {}

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->sectors->paginate($perPage);
    }

    public function all(): Collection
    {
        return $this->sectors->all();
    }

    public function create(array $data): BusinessSector
    {
        return $this->sectors->create(['name' => trim($data['name'])]);
    }

    public function update(BusinessSector $sector, array $data): BusinessSector
    {
        return $this->sectors->update($sector, ['name' => trim($data['name'])]);
    }

    public function delete(BusinessSector $sector): void
    {
        if ($this->sectors->hasClients($sector)) {
            throw new DeletionBlockedException('Este ramo de atuação ainda possui clientes.');
        }

        $this->sectors->delete($sector);
    }
}
