<?php

namespace App\Services\Admin;

use App\Exceptions\DeletionBlockedException;
use App\Models\Person;
use App\Repositories\PersonRepository;
use App\Services\AddressService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PersonService
{
    public function __construct(
        private PersonRepository $people,
        private AddressService $addresses,
    ) {}

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->people->paginate($perPage);
    }

    public function create(array $data, ?UploadedFile $photo): Person
    {
        return DB::transaction(function () use ($data, $photo) {
            return $this->people->create([
                ...$this->personAttributes($data),
                'address_id' => $this->addresses->resolve($data)->id,
                'foto_path' => $photo?->store('people', 'public'),
            ]);
        });
    }

    public function update(Person $person, array $data, ?UploadedFile $photo): Person
    {
        return DB::transaction(function () use ($person, $data, $photo) {
            $fotoPath = $person->foto_path;

            if ($photo !== null) {
                if ($fotoPath) {
                    Storage::disk('public')->delete($fotoPath);
                }

                $fotoPath = $photo->store('people', 'public');
            }

            $previousAddressId = $person->address_id;
            $address = $this->addresses->resolve($data);

            $person = $this->people->update($person, [
                ...$this->personAttributes($data),
                'address_id' => $address->id,
                'foto_path' => $fotoPath,
            ]);

            $this->addresses->releaseIfUnused($previousAddressId === $address->id ? null : $previousAddressId);

            return $person;
        });
    }

    public function delete(Person $person): void
    {
        if ($this->people->isLinkedToUser($person)) {
            throw new DeletionBlockedException('Esta pessoa já possui um usuário vinculado.');
        }

        $this->people->delete($person);
        $this->addresses->releaseIfUnused($person->address_id);
    }

    private function personAttributes(array $data): array
    {
        return [
            'nome' => $data['nome'],
            'cpf' => $data['cpf'],
            'rg' => filled($data['rg'] ?? null) ? $data['rg'] : null,
            'nascimento' => $data['nascimento'],
            'sexo' => $data['sexo'],
            'email' => $data['email'],
            'telefone1' => $data['telefone1'],
            'telefone2' => filled($data['telefone2'] ?? null) ? $data['telefone2'] : null,
        ];
    }
}
