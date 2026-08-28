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

    public function mount(): void
    {
        $this->ensureAdminAccess();

        $setting = AppSetting::current();
        $this->company_name = (string) $setting->company_name;
        $this->logo_size = $setting->logo_size->value;
    }

    public function save(): void
    {
        $this->authorize('accessAdminPanel', User::class);

        $validated = $this->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'logo_size' => ['required', new Enum(LogoSize::class)],
        ]);

        $setting = AppSetting::current();
        $setting->company_name = $this->company_name !== '' ? $this->company_name : null;
        $setting->logo_size = LogoSize::from($validated['logo_size']);

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
        ]);
    }
}
