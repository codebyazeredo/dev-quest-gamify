<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const DEFAULT_SLA_HOURS = 48;

    public function up(): void
    {
        DB::table('tickets')->whereNull('due_at')->orderBy('id')->each(function (object $ticket) {
            DB::table('tickets')->where('id', $ticket->id)->update([
                'due_at' => Carbon::parse($ticket->created_at)->addHours(self::DEFAULT_SLA_HOURS),
            ]);
        });
    }

    public function down(): void {}
};
