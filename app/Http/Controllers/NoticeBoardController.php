<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notice;
use App\Models\Registration;
use App\Models\Employee;
use App\Models\Employee_department;
use App\Http\Controllers\NoticeBoardController;
use Sentinel;
use DateTime;

class NoticeBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$datum = new DateTime('now');    /* današnji dan */
		$datum->modify('-8 day');
		
		$notices = Notice::orderBy('created_at','DESC')->where('created_at', '>', $datum)->get();
	//	$notices = Notice::orderBy('created_at','DESC')->get();
		
		$user = Sentinel::getUser();
		$employee = Employee::where('first_name',$user->first_name)->where('last_name',$user->last_name)->first();

		if($employee) {
			$employee_departments = Employee_department::where('employee_id',$employee->id )->get();
			return view('admin.noticeBoard',['notices'=>$notices, 'employee_departments' => $employee_departments, 'employee' => $employee ]);
		} else {
			$employee_departments = Employee_department::get();
			return view('admin.noticeBoard',['notices'=>$notices]);
		}

		
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
