<?php

namespace Tests\Feature;

use App\Livewire\Board\Create;
use App\Livewire\Board\Index;
use App\Models\Board;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_board_with_default_columns_seeded(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name', 'New Board')
            ->set('description', 'A board')
            ->call('save');

        $board = Board::where('name', 'New Board')->first();

        $this->assertNotNull($board);
        $this->assertSame(6, $board->columns()->count());
    }

    public function test_developer_cannot_create_board(): void
    {
        $developer = User::factory()->developer()->create();

        Livewire::actingAs($developer)
            ->test(Index::class)
            ->call('toggleCreate')
            ->assertForbidden();
    }

    public function test_non_admin_cannot_view_inactive_board_via_direct_url(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->inactive()->create();

        $response = $this->actingAs($developer)->get(route('boards.show', $board));

        $response->assertForbidden();
    }

    public function test_index_hides_inactive_boards_from_non_admins(): void
    {
        $developer = User::factory()->developer()->create();
        $activeBoard = Board::factory()->create(['name' => 'Active Board']);
        Board::factory()->inactive()->create(['name' => 'Inactive Board']);

        Livewire::actingAs($developer)
            ->test(Index::class)
            ->assertSee('Active Board')
            ->assertDontSee('Inactive Board');
    }

    public function test_landing_url_goes_straight_to_the_single_active_board(): void
    {
        $board = Board::factory()->create();

        $this->assertSame(route('boards.show', $board), Board::landingUrl());
    }

    public function test_landing_url_goes_to_the_index_when_there_is_no_active_board(): void
    {
        $this->assertSame(route('boards.index'), Board::landingUrl());
    }

    public function test_landing_url_goes_to_the_index_when_there_are_multiple_active_boards(): void
    {
        Board::factory()->count(2)->create();

        $this->assertSame(route('boards.index'), Board::landingUrl());
    }

    public function test_root_redirects_guests_to_login_and_authenticated_users_to_the_landing_board(): void
    {
        $this->get('/')->assertRedirect(route('login'));

        $board = Board::factory()->create();
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/')->assertRedirect(route('boards.show', $board));
    }
}
