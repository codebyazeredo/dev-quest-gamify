<?php

namespace App\Http\Requests\Gamification;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CheckinHistoryRequest extends FormRequest
{
    private const MONTH_FORMAT = 'Y-m';

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'month' => ['nullable', 'date_format:'.self::MONTH_FORMAT],
        ];
    }

    /** Primeiro dia do mês pedido (ou do mês atual). */
    public function month(): Carbon
    {
        $month = $this->validated('month');

        return $month !== null
            ? Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfDay()
            : Carbon::instance(now())->startOfMonth();
    }
}
