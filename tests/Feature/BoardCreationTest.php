<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_board_seeds_the_standard_columns_by_default(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.boards.store'), ['name' => 'Squad Alpha', 'is_active' => true])->assertCreated();

        $board = Board::where('name', 'Squad Alpha')->firstOrFail();

        $this->assertSame(7, $board->columns()->count());
    }

    public function test_unchecking_seed_default_columns_creates_an_empty_board(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.boards.store'), ['name' => 'Squad Bravo', 'is_active' => true, 'seed_default_columns' => false])
            ->assertCreated();

        $board = Board::where('name', 'Squad Bravo')->firstOrFail();

        $this->assertSame(0, $board->columns()->count());
    }
}
