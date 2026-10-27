<?php

namespace App\Services;

use App\Models\Client;
use App\Repositories\ClientRepository;

/** Identifica o cliente do portal pelo CNPJ e pelo e-mail cadastrado. */
class PortalAuthService
{
    public function __construct(private ClientRepository $clients) {}

    public function attempt(string $cnpj, string $email): ?Client
    {
        return $this->clients->findActiveByCnpjAndEmail(preg_replace('/\D/', '', $cnpj), trim($email));
    }
}
