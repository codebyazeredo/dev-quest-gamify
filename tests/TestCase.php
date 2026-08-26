<?php

namespace Tests;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Every role-based factory state (admin()/productOwner()/developer()/
        // tester()/suporte()) calls assignRole(), which requires the role row
        // to exist — seed it automatically for any test that has a database.
        if (in_array(RefreshDatabase::class, class_uses_recursive($this), true)) {
            // Spatie caches role/permission assignments in the array cache
            // store, which — unlike the database — is NOT reset by
            // RefreshDatabase between tests, so stale data from a previous
            // test's (already-truncated) rows can leak in and cause flaky
            // authorization results. Clear it before every test.
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $this->seed(RoleSeeder::class);
        }
    }
}
