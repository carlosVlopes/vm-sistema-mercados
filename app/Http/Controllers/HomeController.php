<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public static function index()
    {
        return view('home');
    }
}
