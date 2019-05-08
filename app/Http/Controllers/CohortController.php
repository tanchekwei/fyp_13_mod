<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cohort;
use App\Staff;
use App\Supervisor_Cohort;
use App\Student;
use DB;
use Session;

class CohortController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct() {
        $this->middleware('auth:staff');
    }

    public function index()
    {
        return view('MaintainCohort');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cohort = new Cohort();
        $cohort->cohortId = $request->get('cohortid');
        $cohort->project1startingDate =  $request->get('p1startdate');
        $cohort->project1endDate = $request->get('p1enddate');
        $cohort->project2startingDate = $request->get('p2startdate');
        $cohort->project2endDate = $request->get('p2enddate');
        $cid = $cohort->cohortId;
        $cohort->save();
        \error_log($cohort);
        
        return redirect()->action('CohortController@showall')->with('success','New information was created.');
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
        $cohort = Cohort::find($id);
        return view('UpdateCohort',compact('cohort','id'));
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
        $cohort = Cohort::find($id);
        $cohort->cohortId = $request->get('cohortid');
        $cohort->project1startingDate =  $request->get('p1startdate');
        $cohort->project1endDate = $request->get('p1enddate');
        $cohort->project2startingDate = $request->get('p2startdate');
        $cohort->project2endDate = $request->get('p2enddate');
        $cohort->save();
        
        $result = Cohort::all();
        return redirect()->action('CohortController@showall')->with('success','Information was updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $check = 0;
        $havestudent = Student::where('cohortId','=',$request->deleteid)->get();
        if(!$havestudent->isEmpty())
        {
            $check+=1;
        }
        $havestaff = Supervisor_Cohort::where('cohortId','=',$request->deleteid)->get();
        if(!$havestaff->isEmpty())
        {
            $check+=1;
        }
        
        if($check!=0)
        {
            Session::flash('error', 'Delete was deny due to there have information inside.');
            return back();
        }
        else
        {
            $cohort = Cohort::find($request->deleteid);
            $cohort->delete();
            Session::flash('success', 'The information was deleted.');
            return back();
        }
    }
    
    public function showall()
    {
        $result = Cohort::orderBy('cohortId','desc')->get();
        return view('MaintainCohort',compact('result'));
    }
    
    public function orderby($order)
    {
        $result = Cohort::orderBy($order,'desc')->get();
        return view('MaintainCohort',compact('result'));
    }    
    
    public function showmenu($id)
    {
        $cid = $id;
        Session::put("cohortId",$cid);
        return view('index',compact('cid'));
    }
    
    public function showaddsupervisor()
    {
        
    }
    
    //tee ren mian
    public function studSpvList()
    {
        $cohort = Cohort::all();
        return view('StudentSupervisorList', compact('cohort'));
    }
}
