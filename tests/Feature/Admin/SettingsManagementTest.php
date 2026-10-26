<?php

namespace Tests\Feature\Admin;

use App\Enums\LogoSize;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'company_name' => 'Acme Corp',
            'logo_size' => LogoSize::LARGE->value,
            'primary_color' => AppSetting::DEFAULT_PRIMARY_COLOR,
            ...$overrides,
        ];
    }

    public function test_admin_can_update_the_company_name_and_logo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('api.settings.update'), $this->payload(['_method' => 'PUT', 'logo' => UploadedFile::fake()->image('logo.png')]), ['Accept' => 'application/json'])
            ->assertOk();

        $setting = AppSetting::current();

        $this->assertSame('Acme Corp', $setting->company_name);
        $this->assertNotNull($setting->logo_path);
        Storage::disk('public')->assertExists($setting->logo_path);
    }

    public function test_logo_defaults_to_large_and_admin_can_change_it(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertSame(LogoSize::LARGE, AppSetting::current()->logo_size);

        $this->actingAs($admin)
            ->putJson(route('api.settings.update'), $this->payload(['logo_size' => LogoSize::SMALL->value]))
            ->assertOk();

        $this->assertSame(LogoSize::SMALL, AppSetting::current()->logo_size);
    }

    public function test_admin_can_change_the_primary_color_and_invalid_values_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertSame(AppSetting::DEFAULT_PRIMARY_COLOR, AppSetting::current()->primaryColor());

        $this->actingAs($admin)
            ->putJson(route('api.settings.update'), $this->payload(['primary_color' => 'azul']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('primary_color');

        $this->actingAs($admin)
            ->putJson(route('api.settings.update'), $this->payload(['primary_color' => '#198754']))
            ->assertOk();

        $this->assertSame('#198754', AppSetting::current()->primaryColor());
    }

    public function test_developer_is_forbidden_on_the_page_and_on_the_api(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/admin/settings')->assertForbidden();
        $this->actingAs($developer)->putJson(route('api.settings.update'), $this->payload())->assertForbidden();
    }
}
