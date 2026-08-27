<?php

namespace App\Livewire\Admin\People;

use App\Enums\Gender;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\Person;
use App\Repositories\PersonRepository;
use App\Rules\ValidCpf;
use App\Services\Admin\PersonService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use FlushesToasts;
    use WithFileUploads;

    public Person $person;

    public string $nome = '';

    public string $cpf = '';

    public string $rg = '';

    public string $nascimento = '';

    public string $sexo = '';

    public string $email = '';

    public string $telefone1 = '';

    public string $telefone2 = '';

    public mixed $foto = null;

    public string $cep = '';

    public string $logradouro = '';

    public string $numero = '';

    public string $cidade = '';

    public string $estado = '';

    public function mount(int $personId): void
    {
        $this->person = app(PersonRepository::class)->findWithAddressOrFail($personId);

        $this->authorize('update', $this->person);

        $this->nome = $this->person->nome;
        $this->cpf = $this->person->cpf;
        $this->rg = (string) $this->person->rg;
        $this->nascimento = $this->person->nascimento->format('Y-m-d');
        $this->sexo = $this->person->sexo->value;
        $this->email = $this->person->email;
        $this->telefone1 = $this->person->telefone1;
        $this->telefone2 = (string) $this->person->telefone2;

        $address = $this->person->address;
        $this->cep = $address?->cep ?? '';
        $this->logradouro = $address?->logradouro ?? '';
        $this->numero = (string) $address?->numero;
        $this->cidade = $address?->cidade ?? '';
        $this->estado = $address?->estado ?? '';
    }

    protected function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:150'],
            'cpf' => ['required', 'string', 'max:14', new ValidCpf, 'unique:people,cpf,'.$this->person->id],
            'rg' => ['nullable', 'string', 'max:20', 'unique:people,rg,'.$this->person->id],
            'nascimento' => ['required', 'date'],
            'sexo' => ['required', new Enum(Gender::class)],
            'email' => ['required', 'email', 'max:255'],
            'telefone1' => ['required', 'string', 'max:20'],
            'telefone2' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'cep' => ['required', 'string', 'max:9'],
            'logradouro' => ['required', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'cidade' => ['required', 'string', 'max:120'],
            'estado' => ['required', 'string', 'size:2'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->person);

        $validated = $this->validate();

        $person = app(PersonService::class)->update($this->person, $validated, $this->foto);

        $this->toastSuccess('Pessoa atualizada', "\"{$person->nome}\" foi atualizada.");
        $this->flushToasts();

        $this->dispatch('person-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.people.edit', [
            'generos' => Gender::cases(),
        ]);
    }
}
