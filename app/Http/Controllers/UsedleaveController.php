<?php

namespace App\Http\Controllers;

use App\Usedleave;
use Illuminate\Http\Request;

class UsedleaveController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Usedleave  $usedleave
     * @return \Illuminate\Http\Response
     */
    public function show(Usedleave $usedleave)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Usedleave  $usedleave
     * @return \Illuminate\Http\Response
     */
    public function edit(Usedleave $usedleave)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Usedleave  $usedleave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Usedleave $usedleave)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Usedleave  $usedleave
     * @return \Illuminate\Http\Response
     */
    public function destroy(Usedleave $usedleave)
    {
        //
    }
}
