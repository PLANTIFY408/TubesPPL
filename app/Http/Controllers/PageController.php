<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function showHome()
    {
        return view('home');
    }
}
