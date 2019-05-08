<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Faculty;
use App\Supervisor_Cohort;
use App\Staff;

class SupcohortController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() 
    {
        $this->middleware('auth:staff');
    }
    
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supresult = DB::select(DB::raw('select sc.cohortId, sc.staffId, s.staffName, sc.moderatorId,
                                        (select staffName from staff where staffId = sc.moderatorId) as moderatorName
                                        from supervisor_cohort sc, staff s
                                        where sc.staffId = s.staffId
                                        and sc.cohortId = :id;'),array('id'=>$id));
        $cid = $id;
        return view('StaffPairing',compact('supresult','cid'));
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
    
    public function addsupervisorpage($id,$staffId)
    {       
        $supresult = DB::select(DB::raw("select sc.cohortId, sc.staffId, s.staffName, sc.moderatorId,
                                            (select staffName from staff where staffId = sc.moderatorId) as moderatorName
                                            from supervisor_cohort sc, staff s
                                            where sc.staffId = s.staffId
                                            and cohortId = :id;"),array('id'=>$id));
        $stafffaculty= DB::select(DB::raw("select s.*, f.* from staff s, department d, faculty f
                                            where s.departmentId = d.departmentId
                                            and d.facultyId = f.facultyId
                                            and s.staffId = :staffId;"),array('staffId'=>$staffId));
        foreach($stafffaculty as $sf)
        {
            $staffresult = DB::select(DB::raw("select s.*, f.* from staff s, department d, faculty f
                                                where s.departmentId = d.departmentId
                                                and d.facultyId = f.facultyId
                                                and f.facultyId = :facultyId;"),array('facultyId'=>$sf->facultyId));
        }
        $staffresult2 = DB::select(DB::raw("select s.*, f.* from staff s, department d, faculty f
                                                where s.departmentId = d.departmentId
                                                and d.facultyId = f.facultyId;"));
        $facultyresult = Faculty::all();
        $cid = $id;
        return view('AddSupervisor',compact('supresult','staffresult','facultyresult','cid','stafffaculty','staffresult2'));
    }
    
    public function addsupervisor(Request $request)
    {
        $removearr = $request->removearr;
        $addarr = $request->addarr;
        $cid = $request->cid;
        if($removearr !=null)
        {
            for($i = 0 ;$i<sizeof($removearr);$i++)
            {
                $result = Supervisor_Cohort::where('cohortId','=',$cid)->where('staffId','=',$removearr[$i])->delete();
                $staff = Staff::find($removearr[$i]);
                if($staff->role == 'supervisor')
                {
                    $staff->role = 'lecturer';
                    $staff->save();
                }
            }
        }
        
        if($addarr !=null)
        {
            for($i = 0 ;$i<sizeof($addarr);$i++)
            {
                $check = Supervisor_Cohort::where('staffId','=',$addarr[$i])->where('cohortId','=',$cid)->first();
                if(!$check)
                {
                    $result2 = new Supervisor_Cohort();
                    $result2->cohortId = $cid;
                    $result2->staffId = $addarr[$i];
                    $result2->save();
                }
                $staff = Staff::find($addarr[$i]);
                if($staff->role == 'lecturer')
                {
                    $staff->role = 'supervisor';
                    $staff->save();
                }
            }
        }
        
        return response()->json(['success'=>"The Changes have been saved"]);
    }
    
    public function arrstore(Request $request)
    {
        $array = $request->arr;
        for($i = 0; $i<sizeof($array);$i++)
        {
            $sp = Supervisor_Cohort::where('cohortId','=',$array[$i]['cohortId'])->where('staffId','=',$array[$i]['staffId'])->update(['moderatorId'=>$array[$i]['moderatorId']]);
        }
    }
}
