<?php

namespace App\Http\Controllers;

use App\Wagehike;
use DB;
use Illuminate\Http\Request;

class WagehikeController extends Controller
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
        $Increment = DB::SELECT("SELECT A.id, employee_id, first_name, last_name, designation, department, section, work_location, start_date, 
        employee_image, effective_date, amount, previous_basic, file_link, updated_at, next_increment, MONTHNAME(next_increment)next_increment_month, basic_pay, total_salary salary FROM (
        SELECT A.id, A.employee_id, first_name, last_name, designation, department, section, work_location, start_date, 
        employee_image, effective_date, amount, previous_basic, file_link, updated_at, total_salary, basic_pay,
        (CASE WHEN effective_date IS null THEN (CASE WHEN work_location != 'Management' THEN DATE_ADD(start_date, INTERVAL 6 MONTH) ELSE DATE_ADD(start_date, INTERVAL 12 MONTH) END) ELSE DATE_ADD(effective_date, INTERVAL 12 MONTH) END)next_increment FROM (
            SELECT id, employee_id, first_name, last_name, designation, department, section, work_location, start_date, employee_image FROM employees WHERE deleted_by = 0 AND status = 'active'
            )A LEFT JOIN (SELECT A.effective_date, A.employee_id, next_increment, amount, previous_basic, file_link, updated_at FROM (
                SELECT MAX(effective_date)effective_date, employee_id FROM wagehikes GROUP BY employee_id
                )A LEFT JOIN (SELECT effective_date, employee_id, next_increment, amount, previous_basic, file_link, updated_at FROM wagehikes
                )B ON A.effective_date = B.effective_date AND A.employee_id = B.employee_id
            )B ON A.id = B.employee_id LEFT JOIN (SELECT basic_pay, medic_alw, house_rent, ta, da, providant_fund, tax, total_salary, bank_name, acc_no, employee_id FROM salaries
            )C ON A.id = C.employee_id
        )A ORDER BY next_increment");

        return compact('Increment');
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
        $Wagehike = $request->user()->wagehike()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'Wagehike' => $Wagehike
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Wagehike  $wagehike
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Wagehike = DB::SELECT("SELECT id, effective_date, next_increment, amount, previous_basic, (previous_basic*1.5 + 574)previous_gross, 
            (previous_basic*(1+amount/100))post_basic, (previous_basic*(1+amount/100)*1.5 + 574)post_gross, file_link, employee_id, updated_at 
            FROM wagehikes WHERE employee_id = ? ORDER BY effective_date", [$id]);

        return compact('Wagehike');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Wagehike  $wagehike
     * @return \Illuminate\Http\Response
     */
    public function edit(Wagehike $wagehike)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Wagehike  $wagehike
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wagehike $wagehike)
    {
        $wagehike->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Wagehike  $wagehike
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wagehike $wagehike)
    {
        $wagehike->delete();
    }
}
