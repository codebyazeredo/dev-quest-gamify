<?php

namespace App\Enums;

/**
 * The 5 "well-known" role names this app ships with, seeded by RoleSeeder into
 * spatie/laravel-permission's `roles` table. This is NOT a database-cast enum
 * anymore — a user's actual roles live in the `model_has_roles` pivot table and
 * are fully admin-editable at /admin/roles (including adding roles beyond these
 * 5). This enum only gives type-safe references to these specific role names
 * where application code needs to check for one of them by name.
 */
enum UserRole: string
{
    case ADMIN = 'admin';
    case PRODUCT_OWNER = 'product_owner';
    case DEVELOPER = 'dev';
    case TESTER = 'tester';
    case SUPORTE = 'suporte';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::PRODUCT_OWNER => 'Product Owner',
            self::DEVELOPER => 'Desenvolvedor',
            self::TESTER => 'Testes',
            self::SUPORTE => 'Suporte',
        };
    }

    /**
     * PT-BR label for any role name, including custom roles created by an admin
     * beyond the 5 well-known ones above (falls back to a title-cased version
     * of the raw role name).
     */
    public static function labelFor(string $roleName): string
    {
        $known = self::tryFrom($roleName);

        if ($known !== null) {
            return $known->label();
        }

        return \Illuminate\Support\Str::headline($roleName);
    }
}
