<?php

namespace App\Livewire\Admin\People;

use App\Enums\Gender;
use App\Livewire\Concerns\FlushesToasts;
use App\Models\Address;
use App\Models\Person;
use App\Rules\ValidCpf;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
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

        DB::transaction(function () use ($validated) {
            $fotoPath = $this->foto !== null ? $this->foto->store('people', 'public') : null;

            $person = Person::create([
                'nome' => $validated['nome'],
                'cpf' => $validated['cpf'],
                'rg' => $validated['rg'] !== '' ? $validated['rg'] : null,
                'nascimento' => $validated['nascimento'],
                'sexo' => $validated['sexo'],
                'email' => $validated['email'],
                'telefone1' => $validated['telefone1'],
                'telefone2' => $validated['telefone2'] !== '' ? $validated['telefone2'] : null,
                'foto_path' => $fotoPath,
            ]);

            Address::create([
                'person_id' => $person->id,
                'cep' => $validated['cep'],
                'logradouro' => $validated['logradouro'],
                'numero' => $validated['numero'] !== '' ? $validated['numero'] : null,
                'cidade' => $validated['cidade'],
                'estado' => strtoupper($validated['estado']),
            ]);
        });

        $this->toastSuccess('Pessoa criada', "\"{$validated['nome']}\" foi criada.");
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
