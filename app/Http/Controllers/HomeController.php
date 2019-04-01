<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = Location::where('deleted_at',NULL)->get();
        return view('home')->with('locations',$locations);
    }

    public function notFound()
    {
        return view('404');
    }
}
