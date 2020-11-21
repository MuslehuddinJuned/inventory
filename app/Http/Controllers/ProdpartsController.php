<?php

namespace App\Http\Controllers;

use App\Prodparts;
use DB;
use Illuminate\Http\Request;

class ProdpartsController extends Controller
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
        $PoList = DB::SELECT("SELECT id value, po_no text FROM polists WHERE deleted_by = 0");

        return compact('PoList');
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
        $prodparts = $request->user()->prodparts()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'id' => $prodparts->id
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Prodparts  $prodparts
     * @return \Illuminate\Http\Response
     */
    public function show($value)
    {
        $date = substr($value,-8);
        $po_id = strtok($value, '_');

        if($po_id == $value){
            $Production = DB::SELECT("SELECT A.id, po_no, product_code, item, item_code, item_image,  specification, po_qty, parts_qty, unit, total_prod_qty, quantity, prod_date, A.department, remarks FROM(
                SELECT id, quantity, prod_date, department, remarks, producthead_id, productdetails_id, polist_id FROM prodparts WHERE DATE(prod_date) = ?
                )A LEFT JOIN (SELECT id, quantity po_qty, po_no,  producthead_id FROM polists
                )B ON A.polist_id = B.id LEFT JOIN (SELECT id producthead_id, buyer, product_style, product_code FROM productheads
                )C ON B.producthead_id = C.producthead_id LEFT JOIN(SELECT id productdetails_id, sn,  unit_weight, quantity parts_qty, producthead_id, inventory_id FROM productdetails
                )D ON C.producthead_id = D.producthead_id AND A.productdetails_id = D.productdetails_id LEFT JOIN (SELECT id inventory_id, store_id, item, item_code, item_image, specification, cann_per_sheet, grade,weight, unit FROM inventories
                )E ON D.inventory_id = E.inventory_id LEFT JOIN (SELECT SUM(quantity) total_prod_qty , department, productdetails_id, polist_id FROM prodparts WHERE DATE(prod_date) < ? GROUP BY department, productdetails_id, polist_id
                )F ON D.productdetails_id = F.productdetails_id AND B.id = F.polist_id ORDER BY po_no", [$value, $value]);
            return compact('Production');
        }
        $Production = DB::SELECT("SELECT E.id, CONCAT(item_code, ' || ', item, ' || ', specification)text, A.id polist_id, C.productdetails_id,C.productdetails_id value, quantity, po_no,  A.producthead_id, buyer, product_style, product_code, sn, item, item_code, specification, po_qty, parts_qty, unit, total_prod_qty, prod_date, E.department, remarks FROM (
            SELECT id, quantity po_qty, po_no,  producthead_id FROM polists WHERE id = ?
            )A LEFT JOIN (SELECT id producthead_id, buyer, product_style, product_code FROM productheads
            )B ON A.producthead_id = B.producthead_id LEFT JOIN(SELECT id productdetails_id, sn,  unit_weight, quantity parts_qty, producthead_id, inventory_id FROM productdetails
            )C ON B.producthead_id = C.producthead_id LEFT JOIN (SELECT id inventory_id, store_id, item, item_code, specification, cann_per_sheet, grade,weight, unit FROM inventories
            )D ON C.inventory_id = D.inventory_id LEFT JOIN (SELECT id, quantity, prod_date, department, remarks, producthead_id, productdetails_id, polist_id FROM prodparts WHERE DATE(prod_date) = ?
            )E ON C.productdetails_id = E.productdetails_id AND A.id = E.polist_id LEFT JOIN (SELECT SUM(quantity) total_prod_qty , department, productdetails_id, polist_id FROM prodparts WHERE DATE(prod_date) < ? GROUP BY department, productdetails_id, polist_id
            )F ON C.productdetails_id = F.productdetails_id AND A.id = F.polist_id ORDER BY sn", [$po_id, $date, $date]);
        return compact('Production');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Prodparts  $prodparts
     * @return \Illuminate\Http\Response
     */
    public function edit(Prodparts $prodparts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Prodparts  $prodparts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Prodparts $prodparts)
    {
        $Prodparts = Prodparts::find($request['id']);
        $Prodparts->quantity = $request['quantity'];
        $Prodparts->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Prodparts  $prodparts
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::SELECT("DELETE FROM prodparts WHERE id = ?", [$id]);
    }
}
