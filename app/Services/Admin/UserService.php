<?php

namespace App\Services\Admin;

use App\Exceptions\DeletionBlockedException;
use App\Exceptions\DuplicateEntryException;
use App\Models\User;
use App\Repositories\PersonRepository;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(
        private UserRepository $users,
        private PersonRepository $people,
    ) {}

    public function create(int $personId, string $email, string $password, array $roles): User
    {
        $person = $this->people->findOrFail($personId);

        if ($this->people->isLinkedToUser($person)) {
            throw new DuplicateEntryException('Esta pessoa já possui um usuário.');
        }

        $user = $this->users->create([
            'name' => $person->nome,
            'email' => $email,
            'password' => $password,
            'person_id' => $person->id,
        ]);

        $this->users->syncRoles($user, $roles);

        return $user;
    }

    public function update(User $user, string $email, ?string $password, array $roles): User
    {
        $attributes = ['email' => $email];

        if ($password !== null && $password !== '') {
            $attributes['password'] = $password;
        }

        $user = $this->users->update($user, $attributes);

        $this->users->syncRoles($user, $roles);

        return $user;
    }

    public function delete(User $user): void
    {
        if ($this->users->hasXpTransactions($user)) {
            throw new DeletionBlockedException('Este usuário já possui histórico de atividade.');
        }

        $this->users->delete($user);
    }
}
