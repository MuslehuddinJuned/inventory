<?php

namespace App\Http\Controllers;

use App\Rechead;
use Illuminate\Http\Request;
use DB;

class RecheadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Rechead = DB::SELECT('SELECT A.id, requisition_no, remarks, accept, updated_at, store_name FROM (
            SELECT id, requisition_no, remarks, accept, updated_at FROM recheads
            )A LEFT JOIN (
            SELECT inventory_id, rechead_id FROM recdetails
            )B ON A.id = B.rechead_id LEFT JOIN(
            SELECT id, store_name FROM inventories
            )C ON B.inventory_id = C.id GROUP BY A.id ORDER BY updated_at DESC');

        return compact('Rechead');
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
        $Rechead = $request->user()->rechead()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'RecheadID' => $Rechead->id
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function show(Rechead $rechead)
    {
        return 'er';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function edit(Rechead $rechead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rechead $rechead)
    {
        $rechead->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rechead $rechead)
    {
        $rechead->delete();
    }
}
