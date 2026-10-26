<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Contracts\View\View;

class PersonPageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Person::class);

        return view('admin.people.index');
    }
}
