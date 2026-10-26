<?php

namespace App\Services\Admin;

use App\Exceptions\DeletionBlockedException;
use App\Models\Person;
use App\Repositories\AddressRepository;
use App\Repositories\PersonRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PersonService
{
    public function __construct(
        private PersonRepository $people,
        private AddressRepository $addresses,
    ) {}

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->people->paginate($perPage);
    }

    public function create(array $data, ?UploadedFile $photo): Person
    {
        return DB::transaction(function () use ($data, $photo) {
            $person = $this->people->create([...$this->personAttributes($data), 'foto_path' => $photo?->store('people', 'public')]);

            $this->addresses->updateOrCreateForPerson($person->id, $this->addressAttributes($data));

            return $person;
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

            $person = $this->people->update($person, [
                ...$this->personAttributes($data),
                'foto_path' => $fotoPath,
            ]);

            $this->addresses->updateOrCreateForPerson($person->id, $this->addressAttributes($data));

            return $person;
        });
    }

    public function delete(Person $person): void
    {
        if ($this->people->isLinkedToUser($person)) {
            throw new DeletionBlockedException('Esta pessoa já possui um usuário vinculado.');
        }

        $this->people->delete($person);
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

    private function addressAttributes(array $data): array
    {
        return [
            'cep' => $data['cep'],
            'logradouro' => $data['logradouro'],
            'numero' => filled($data['numero'] ?? null) ? $data['numero'] : null,
            'cidade' => $data['cidade'],
            'estado' => strtoupper($data['estado']),
        ];
    }
}
