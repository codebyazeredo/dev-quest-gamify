<?php

namespace App\Http\Requests\Ticket;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class TicketAssigneeRequest extends FormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                function (string $attribute, mixed $value, Closure $fail) {
                    if ($value !== null && ! User::find($value)?->isSupportStaff()) {
                        $fail('O usuário escolhido não faz parte da equipe de suporte.');
                    }
                },
            ],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['user_id' => 'responsável'];
    }

    public function assigneeId(): ?int
    {
        return $this->filled('user_id') ? $this->integer('user_id') : null;
    }
}
