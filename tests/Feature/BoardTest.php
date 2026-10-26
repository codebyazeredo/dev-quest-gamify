<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\User;
use App\Services\BoardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_board_with_default_columns_seeded(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('api.boards.store'), ['name' => 'New Board', 'description' => 'A board', 'is_active' => true])
            ->assertCreated()
            ->assertJsonPath('data.name', 'New Board');

        $board = Board::where('name', 'New Board')->firstOrFail();

        $this->assertSame(7, $board->columns()->count());
    }

    public function test_admin_can_update_a_board(): void
    {
        $admin = User::factory()->admin()->create();
        $board = Board::factory()->create(['name' => 'Old']);

        $this->actingAs($admin)
            ->putJson(route('api.boards.update', $board), ['name' => 'Renamed', 'description' => null, 'is_active' => true])
            ->assertOk();

        $this->assertSame('Renamed', $board->refresh()->name);
    }

    public function test_developer_cannot_create_board(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)
            ->postJson(route('api.boards.store'), ['name' => 'New Board', 'is_active' => true])
            ->assertForbidden();

        $this->assertNull(Board::where('name', 'New Board')->first());
    }

    public function test_board_name_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        Board::factory()->create(['name' => 'Squad']);

        $this->actingAs($admin)
            ->postJson(route('api.boards.store'), ['name' => 'Squad', 'is_active' => true])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_non_admin_cannot_view_inactive_board_via_direct_url(): void
    {
        $developer = User::factory()->developer()->create();
        $board = Board::factory()->inactive()->create();

        $this->actingAs($developer)->get(route('boards.show', $board))->assertForbidden();
    }

    public function test_index_hides_inactive_boards_from_non_admins(): void
    {
        $developer = User::factory()->developer()->create();
        Board::factory()->create(['name' => 'Active Board']);
        Board::factory()->inactive()->create(['name' => 'Inactive Board']);

        $this->actingAs($developer)->get(route('boards.index'))
            ->assertOk()
            ->assertSee('Active Board')
            ->assertDontSee('Inactive Board');
    }

    public function test_landing_url_goes_straight_to_the_single_active_board(): void
    {
        $board = Board::factory()->create();

        $this->assertSame(route('boards.show', $board), app(BoardService::class)->landingUrl());
    }

    public function test_landing_url_goes_to_the_index_when_there_is_no_active_board(): void
    {
        $this->assertSame(route('boards.index'), app(BoardService::class)->landingUrl());
    }

    public function test_landing_url_goes_to_the_index_when_there_are_multiple_active_boards(): void
    {
        Board::factory()->count(2)->create();

        $this->assertSame(route('boards.index'), app(BoardService::class)->landingUrl());
    }

    public function test_root_redirects_guests_to_login_and_authenticated_users_to_the_landing_board(): void
    {
        $this->get('/')->assertRedirect(route('login'));

        $board = Board::factory()->create();
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)->get('/')->assertRedirect(route('boards.show', $board));
    }
}
