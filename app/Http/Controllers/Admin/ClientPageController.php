<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Contracts\View\View;

class ClientPageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Client::class);

        return view('admin.clients.index');
    }
}
