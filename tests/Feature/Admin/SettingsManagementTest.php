<?php

namespace Tests\Feature\Admin;

use App\Enums\LogoSize;
use App\Livewire\Admin\Settings;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_the_company_name_and_logo(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Settings::class)
            ->set('company_name', 'Acme Corp')
            ->set('logo', UploadedFile::fake()->image('logo.png'))
            ->call('save');

        $setting = AppSetting::current();
        $this->assertSame('Acme Corp', $setting->company_name);
        $this->assertNotNull($setting->logo_path);
        Storage::disk('public')->assertExists($setting->logo_path);
    }

    public function test_logo_defaults_to_large_and_admin_can_change_it(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertSame(LogoSize::LARGE, AppSetting::current()->logo_size);

        Livewire::actingAs($admin)
            ->test(Settings::class)
            ->set('logo_size', LogoSize::SMALL->value)
            ->call('save');

        $this->assertSame(LogoSize::SMALL, AppSetting::current()->logo_size);
    }

    public function test_developer_gets_forbidden_on_the_route(): void
    {
        $developer = User::factory()->developer()->create();

        $response = $this->actingAs($developer)->get('/admin/settings');

        $response->assertForbidden();
    }

    public function test_livewire_method_rejects_non_admin_even_when_route_middleware_is_bypassed(): void
    {
        $developer = User::factory()->developer()->create();

        Livewire::actingAs($developer)
            ->test(Settings::class)
            ->assertForbidden();
    }
}
