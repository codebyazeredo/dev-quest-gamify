<?php

namespace App\Livewire\Admin;

use App\Enums\LogoSize;
use App\Livewire\Concerns\FlushesToasts;
use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class Settings extends Component
{
    use FlushesToasts;
    use RequiresAdminAccess;
    use WithFileUploads;

    public string $company_name = '';

    public mixed $logo = null;

    public string $logo_size = 'large';

    public string $primary_color = AppSetting::DEFAULT_PRIMARY_COLOR;

    public function mount(): void
    {
        $this->ensureAdminAccess();

        $setting = AppSetting::current();
        $this->company_name = (string) $setting->company_name;
        $this->logo_size = $setting->logo_size->value;
        $this->primary_color = $setting->primaryColor();
    }

    public function save(): void
    {
        $this->authorize('accessAdminPanel', User::class);

        $validated = $this->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'logo_size' => ['required', new Enum(LogoSize::class)],
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ], [
            'primary_color.required' => 'Escolha a cor primária do sistema.',
            'primary_color.regex' => 'A cor primária deve ser um hexadecimal válido no formato #RRGGBB (ex.: #0d6efd).',
        ]);

        $setting = AppSetting::current();
        $setting->company_name = $this->company_name !== '' ? $this->company_name : null;
        $setting->logo_size = LogoSize::from($validated['logo_size']);
        $setting->primary_color = strtolower($validated['primary_color']);

        if ($this->logo !== null) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }

            $setting->logo_path = $this->logo->store('branding', 'public');
        }

        $setting->save();

        $this->logo = null;

        $this->toastSuccess('Configurações salvas', 'As alterações foram aplicadas.');
        $this->flushToasts();
    }

    public function render(): View
    {
        return view('livewire.admin.settings', [
            'setting' => AppSetting::current(),
            'colorPresets' => AppSetting::PRIMARY_COLOR_PRESETS,
        ]);
    }
}
