<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataDiriController extends Controller
{
    public function index()
    {
        $data = [
            'nama'       => 'M Raditya Zauhair',
            'profesi'    => 'Full Stack Developer',
            'email'      => 'radityazauhair@example.com',
            'keahlian'   => ['PHP', 'Laravel', 'HTML & CSS', 'MySQL']
        ];

        return view('datadiri', compact('data'));
    }
}