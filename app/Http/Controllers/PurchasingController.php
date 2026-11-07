<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PurchasingController extends Controller
{
    public function local(): View
    {
        return $this->page('local');
    }

    public function foreign(): View
    {
        return $this->page('foreign');
    }

    public function market(): View
    {
        return $this->page('market');
    }

    private function page(string $section): View
    {
        return view('purchasing.index', compact('section'));
    }
}
