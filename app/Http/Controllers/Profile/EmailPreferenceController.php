<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailPreferenceController extends Controller
{
    public function __construct(private ProfileService $profile) {}

    public function update(Request $request): RedirectResponse
    {
        $request->validate(['email_notifications' => ['required', 'boolean']]);

        $receiveEmails = $request->boolean('email_notifications');

        $this->profile->updateEmailPreference($request->user(), $receiveEmails);

        return redirect()->route('security')->with('status', $receiveEmails
            ? 'Você voltará a receber e-mails de atribuição de tarefas.'
            : 'Você não receberá mais e-mails de atribuição de tarefas. As notificações dentro do sistema continuam.');
    }
}
