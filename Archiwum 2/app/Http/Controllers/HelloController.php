<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelloController extends Controller
{
    public function index(string $name)
    {
        $items = [
            'jeden',
            'dwa',
            'trzy'
        ];

        $html ='<h2>Naglowek</h2>';

        return view('hello.index', [
            'name' => $name,
            'items' =>$items,
            'html' =>$html
        ]);
    }
}
