<?php

namespace App\Http\Requests\Concerns;

/** Campos do endereço compartilhados pelos formulários de pessoa e de perfil. */
trait ValidatesAddress
{
    /** @return array<string, array<int, string>> */
    protected function addressRules(bool $required): array
    {
        $requiredRule = $required ? 'required' : 'nullable';

        return [
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'cep' => [$requiredRule, 'string', 'max:9'],
            'logradouro' => [$requiredRule, 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['nullable', 'string', 'max:120'],
            'cidade' => [$requiredRule, 'string', 'max:120'],
            'estado' => [$requiredRule, 'string', 'size:2'],
            'referencia' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /** @return array<string, string> */
    protected function addressAttributes(): array
    {
        return [
            'country_id' => 'país',
            'cep' => 'CEP',
            'estado' => 'estado (UF)',
            'referencia' => 'ponto de referência',
        ];
    }
}
