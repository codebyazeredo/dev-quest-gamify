<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class Settings extends Component
{
    use RequiresAdminAccess;
    use WithFileUploads;

    public string $company_name = '';

    public mixed $logo = null;

    public function mount(): void
    {
        $this->ensureAdminAccess();

        $this->company_name = (string) AppSetting::current()->company_name;
    }

    public function save(): void
    {
        $this->authorize('accessAdminPanel', User::class);

        $this->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $setting = AppSetting::current();
        $setting->company_name = $this->company_name !== '' ? $this->company_name : null;

        if ($this->logo !== null) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }

            $setting->logo_path = $this->logo->store('branding', 'public');
        }

        $setting->save();

        $this->logo = null;
    }

    public function render(): View
    {
        return view('livewire.admin.settings', [
            'setting' => AppSetting::current(),
        ]);
    }
}
