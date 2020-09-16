<?php

namespace App\Http\Controllers;

use App\Employee;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
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
        $EmployeeList = DB::SELECT("SELECT id, employee_id, CONCAT(CASE WHEN last_name IS NULL THEN '' ELSE last_name END, CASE WHEN (first_name IS NOT NULL AND last_name IS NOT NULL) THEN  ', ' ELSE '' END, CASE WHEN first_name IS NULL THEN '' ELSE first_name END)AS name, first_name, last_name,  address, mobile_no, email, blood_group, gender, date_of_birth, marital_status, designation, department, section, work_location, start_date, salary, contact_name, contact_address, contact_phone, relationship, employee_image, status, user_id, deleted_by, created_at, updated_at
        FROM employees WHERE deleted_by = 0");

        return compact('EmployeeList');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $EmployeeList = DB::SELECT("SELECT id, employee_id, CONCAT(CASE WHEN last_name IS NULL THEN '' ELSE last_name END, CASE WHEN (first_name IS NOT NULL AND last_name IS NOT NULL) THEN  ', ' ELSE '' END, CASE WHEN first_name IS NULL THEN '' ELSE first_name END)AS name, first_name, last_name,  address, mobile_no, email, blood_group, gender, date_of_birth, marital_status, designation, department, section, work_location, start_date, salary, contact_name, contact_address, contact_phone, relationship, employee_image, status, user_id, deleted_by, created_at, updated_at
        FROM employees WHERE deleted_by = 0");

        return $EmployeeList;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id' => ['required',
                                Rule::unique('employees')->ignore(1, 'deleted_by')]
        ]); 

        // $Employee = Employee::create($request->except('image') + [
        //     'user_id' => Auth::id(),
        //     'employee_image' => $anyVariable
        //     ]);
        
        $Employee = new Employee;
        $Employee->employee_id = $request['employee_id'];
        $Employee->employee_image = 'noimage.jpg';
        $Employee->user_id = auth()->user()->id;
        $Employee->save();

        if(request()->expectsJson()){
            return response()->json([
                'TableId' => $Employee->id
            ]);
        }
        // $request->user()->employees()->create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    { 
        $id = $employee->id;
        $this->validate($request, [
            'employee_id' => ['required',
                                Rule::unique('employees')->where(function ($query) use($id) {
                                    return $query->where('id', '<>',  $id)
                                    ->where('deleted_by', 0);
                                }),]
        ]);
        
        if($request->employee_image){
            $exploded = explode(',', $request->employee_image);
            $decoded = base64_decode($exploded[1]);
    
            if(str_contains($exploded[0], 'jpeg'))
                $extesion = 'jpg';
            else
                $extesion = 'png';
    
            $fileName = str_random().'.'.$extesion;
            $path = public_path()."/"."images"."/"."employee"."/".$fileName;
    
            file_put_contents($path, $decoded);

            // delete previous image
            $Employee = Employee::find($employee->id);

            if($Employee->employee_image != 'noimage.jpg'){
                $path = public_path().'/images/employee/'.$Employee->employee_image;
                @unlink($path);
            }

            $employee->update(['employee_image' => $fileName]); 
        }

        else $employee->update($request->all());        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $Employee = Employee::find($employee->id);

        if($Employee->employee_image != 'noimage.jpg'){
            //Delete Image
            $path = public_path().'/images/employee/'.$Employee->employee_image;
            @unlink($path);

        }

        $Employee->deleted_by = 1;
        $Employee->employee_image = 'noimage.jpg';
        $Employee->save();
    }
}