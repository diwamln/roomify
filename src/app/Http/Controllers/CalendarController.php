<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Menampilkan halaman kalender ketersediaan.
     */
    public function index()
    {
        return view('calendar.index');
    }
}