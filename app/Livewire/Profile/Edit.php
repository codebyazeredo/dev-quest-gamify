<?php

namespace App\Livewire\Profile;

use App\Enums\Gender;
use App\Models\Address;
use App\Models\Person;
use App\Models\User;
use App\Rules\ValidCpf;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Self-service "meus dados" screen — always operates on auth()->user(), never
 * accepts a target id, so there is no separate authorization check needed:
 * a user can only ever reach their own Person/Address/User rows through here.
 */
class Edit extends Component
{
    use WithFileUploads;

    public User $user;

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

    /**
     * Seeded/legacy accounts may have no Address row at all (only the People
     * admin Create/Edit flow enforces one). Self-editing shouldn't force a
     * user to fill in a full address just to fix their phone number, so the
     * address block only becomes mandatory once one already exists.
     */
    public bool $hasAddress = false;

    public string $login_email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->user = auth()->user();
        $this->person = $this->user->person()->with('address')->firstOrFail();

        $this->nome = $this->person->nome;
        $this->cpf = $this->person->cpf;
        $this->rg = (string) $this->person->rg;
        $this->nascimento = $this->person->nascimento->format('Y-m-d');
        $this->sexo = $this->person->sexo->value;
        $this->email = $this->person->email;
        $this->telefone1 = $this->person->telefone1;
        $this->telefone2 = (string) $this->person->telefone2;

        $address = $this->person->address;
        $this->hasAddress = $address !== null;
        $this->cep = $address?->cep ?? '';
        $this->logradouro = $address?->logradouro ?? '';
        $this->numero = (string) $address?->numero;
        $this->cidade = $address?->cidade ?? '';
        $this->estado = $address?->estado ?? '';

        $this->login_email = $this->user->email;
    }

    /**
     * @return array<string, mixed>
     */
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
            'cep' => [$this->hasAddress ? 'required' : 'nullable', 'string', 'max:9'],
            'logradouro' => [$this->hasAddress ? 'required' : 'nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'cidade' => [$this->hasAddress ? 'required' : 'nullable', 'string', 'max:120'],
            'estado' => [$this->hasAddress ? 'required' : 'nullable', 'string', 'size:2'],
            'login_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            if ($this->foto !== null) {
                if ($this->person->foto_path) {
                    Storage::disk('public')->delete($this->person->foto_path);
                }

                $this->person->foto_path = $this->foto->store('people', 'public');
            }

            $this->person->fill([
                'nome' => $validated['nome'],
                'cpf' => $validated['cpf'],
                'rg' => $validated['rg'] !== '' ? $validated['rg'] : null,
                'nascimento' => $validated['nascimento'],
                'sexo' => $validated['sexo'],
                'email' => $validated['email'],
                'telefone1' => $validated['telefone1'],
                'telefone2' => $validated['telefone2'] !== '' ? $validated['telefone2'] : null,
            ]);
            $this->person->save();

            if ($this->hasAddress || $validated['cep'] !== '') {
                Address::updateOrCreate(
                    ['person_id' => $this->person->id],
                    [
                        'cep' => $validated['cep'],
                        'logradouro' => $validated['logradouro'],
                        'numero' => $validated['numero'] !== '' ? $validated['numero'] : null,
                        'cidade' => $validated['cidade'],
                        'estado' => strtoupper($validated['estado']),
                    ]
                );
            }

            $this->user->name = $validated['nome'];
            $this->user->email = $validated['login_email'];

            if ($this->password !== '') {
                $this->user->password = $this->password;
            }

            $this->user->save();
        });

        $this->password = '';
        $this->password_confirmation = '';

        $this->dispatch('profile-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.profile.edit', [
            'generos' => Gender::cases(),
        ]);
    }
}
