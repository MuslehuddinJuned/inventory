<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Imports\AttendanceImport;
use DB;
use Excel;
use Illuminate\Http\Request;

class AttendanceController extends Controller
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
        return 'hi';
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
        
        $exploded = explode(',', $request->uploadFile);
        $decoded = base64_decode($exploded[1]);

        if(str_contains($exploded[0], 'spreadsheetml'))
            $extesion = 'xlsx';
        else
            $extesion = 'xls';

        $fileName = str_random().'.'.$extesion;
        $path = public_path().'/file/attendance/'.$fileName;
        file_put_contents($path, $decoded);
        // $path = '/home/sustipe/inventory.sustipe.com/file/attendance/'.$fileName;

        // $data = Excel::import(new AttendanceImport, $path);
        $data = Excel::toArray(new AttendanceImport, $path);
        if(count($data) > 0) {
            foreach($data as $key => $value)
            {
                foreach($value as $row)
                {
                    $insert_data[] = array(
                        'ac_no'     => $row['AC-No'],
                        'name'      => $row['Name'],
                        'department'=> $row['Department'],
                        'date'      => date_create($row['Date']),
                        'time'      => $row['Time'],
                        'in_time_1' => substr($row['Time'],0,5),
                        'out_time_1'=> substr($row['Time'],6,5),
                        'in_time_2' => substr($row['Time'],12,5),
                        'out_time_2'=> substr($row['Time'],strrpos($row['Time']," ") + 1,5)
                    );
                }
            }
        }

        if(!empty($insert_data)) {
            DB::table('attendances')->insert($insert_data);
        }
        
        // @unlink('/storage/framework/laravel-excel/laravel-excel-'.$fileName);
        @unlink($path);

        // if(request()->expectsJson()){
        //     return response()->json([
        //         'attendance' => $myArray[0]
        //     ]);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show($attendance)
    {
        $date = date_create($attendance);
        $Attendance = Attendance::where('date', $date)->orderBy('ac_no', 'asc')->get();

        return compact('Attendance');
    }

    public function daily($attendance)
    {
        $date = date_create($attendance);
        $Attendance = DB::SELECT("SELECT employee_id, first_name, last_name, designation, department, date, time, in_time_1, in_time_2, out_time_1, out_time_2, ot, ot_extra FROM(
            SELECT id, employee_id, first_name, last_name, designation, department FROM employees WHERE deleted_by = 0
            )A LEFT JOIN (SELECT id, ac_no, date, time, in_time_1, in_time_2, out_time_1, out_time_2, ot, ot_extra FROM attendances WHERE date = ?
            )B ON A.employee_id = B.ac_no ORDER BY employee_id", [$date]);

        return compact('Attendance');
    }

    public function personnel($id, $attendance)
    {
        $date = date_create($attendance);
        $Attendance = Attendance::where('date', $date)->orderBy('ac_no', 'asc')->get();

        return compact('Attendance');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        $attendance->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy($date)
    {
        DB::SELECT('DELETE FROM attendances WHERE date = ?', [$date]);
    }
}
