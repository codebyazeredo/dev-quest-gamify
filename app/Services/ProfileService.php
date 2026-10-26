<?php

namespace App\Services;

use App\Models\Person;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\PersonRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/** Autoatendimento: o usuário logado altera os próprios dados pessoais, endereço e acesso. */
class ProfileService
{
    public function __construct(
        private PersonRepository $people,
        private AddressRepository $addresses,
        private UserRepository $users,
    ) {}

    public function personOf(User $user): Person
    {
        return $this->people->findWithAddressOrFail((int) $user->person_id);
    }

    /**
     * @param  array<string, mixed>  $data  dados validados de pessoa, endereço e acesso (login_email, password)
     */
    public function update(User $user, array $data, ?UploadedFile $photo): Person
    {
        return DB::transaction(function () use ($user, $data, $photo) {
            $person = $this->personOf($user);

            $this->people->update($person, [
                'nome' => $data['nome'],
                'cpf' => $data['cpf'],
                'rg' => $data['rg'] ?? null,
                'nascimento' => $data['nascimento'],
                'sexo' => $data['sexo'],
                'email' => $data['email'],
                'telefone1' => $data['telefone1'],
                'telefone2' => $data['telefone2'] ?? null,
                'foto_path' => $photo !== null ? $this->replacePhoto($person, $photo) : $person->foto_path,
            ]);

            if ($person->address !== null || filled($data['cep'] ?? null)) {
                $this->addresses->updateOrCreateForPerson($person->id, [
                    'cep' => $data['cep'],
                    'logradouro' => $data['logradouro'],
                    'numero' => $data['numero'] ?? null,
                    'cidade' => $data['cidade'],
                    'estado' => strtoupper($data['estado']),
                ]);
            }

            $this->users->update($user, array_filter([
                'name' => $data['nome'],
                'email' => $data['login_email'],
                'password' => $data['password'] ?? null,
            ], fn ($value) => $value !== null));

            return $person->refresh();
        });
    }

    private function replacePhoto(Person $person, UploadedFile $photo): string
    {
        if ($person->foto_path) {
            Storage::disk('public')->delete($person->foto_path);
        }

        return $photo->store('people', 'public');
    }
}
