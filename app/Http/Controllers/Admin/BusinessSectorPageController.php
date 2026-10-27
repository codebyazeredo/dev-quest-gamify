<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSector;
use Illuminate\Contracts\View\View;

class BusinessSectorPageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', BusinessSector::class);

        return view('admin.business-sectors.index');
    }
}
