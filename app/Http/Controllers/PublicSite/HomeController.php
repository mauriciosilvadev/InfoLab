<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Laboratory;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('public.home', [
            'laboratories' => Laboratory::query()
                ->orderBy('name')
                ->get(),
        ]);
    }
}
