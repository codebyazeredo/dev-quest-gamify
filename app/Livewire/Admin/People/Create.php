<?php

namespace App\Livewire\Admin\People;

use App\Enums\Gender;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\Person;
use App\Rules\ValidCpf;
use App\Services\Admin\PersonService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use FlushesToasts;
    use WithFileUploads;

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

    public function mount(): void
    {
        $this->authorize('create', Person::class);
    }

    protected function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:150'],
            'cpf' => ['required', 'string', 'max:14', new ValidCpf, 'unique:people,cpf'],
            'rg' => ['nullable', 'string', 'max:20', 'unique:people,rg'],
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
        $this->authorize('create', Person::class);

        $validated = $this->validate();

        $person = app(PersonService::class)->create($validated, $this->foto);

        $this->toastSuccess('Pessoa criada', "\"{$person->nome}\" foi criada.");
        $this->flushToasts();

        $this->dispatch('person-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.admin.people.create', [
            'generos' => Gender::cases(),
        ]);
    }
}
