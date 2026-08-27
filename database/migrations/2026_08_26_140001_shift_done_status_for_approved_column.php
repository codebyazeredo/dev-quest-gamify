<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tasks')->where('status', 6)->update(['status' => 7]);
        DB::table('board_columns')->where('status', 6)->update(['status' => 7]);
    }

    public function down(): void
    {
        DB::table('tasks')->where('status', 7)->update(['status' => 6]);
        DB::table('board_columns')->where('status', 7)->update(['status' => 6]);
    }
};
