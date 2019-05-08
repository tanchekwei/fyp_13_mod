<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Staff;
use App\Faculty;
use App\Department;
use DB;
use Session;
use Excel;
use File;

class StaffController extends Controller
{
    
    private $msgheader="There have the error(s) when importing the file.<br> "
            . "The error might be:<br> "
            . "Incorrect table column name in excel file.<br>"
            . "dulpicate from database or missing the Staff Id.<br>"
            . "The error(s) detail display as below:<br>";
    
    private $errmsg;
    
    function getErrmsg() {
        return $this->errmsg;
    }

    function setErrmsg($errmsg) {
        $this->errmsg = $errmsg;
    }

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
    public function destroy(Request $request)
    {
        try{
        $staff = Staff::find($request->staffId);
        $staff->status = 'deactive';
        $staff->save();
        }
        catch(Illuminate\Database\QueryException $ex){
            return response()->json($ex->Message);
        }
    }
    
    public function activatestaff(Request $request)
    {
        \error_log($request->staffId);
        try{
        $staff = Staff::find($request->staffId);
        $staff->status = 'active';
        $staff->save();
        }
        catch(Illuminate\Database\QueryException $ex){
            return response()->json($ex->Message);
        }
    }    
    
    public function showall()
    {
        $staffresult = Staff::all();
        return $staffresult;
    }
    
    public function searchbyname(Request $request)
    {
        $result = Staff::where('staffName','like',"%{$request->name}%")->get();
        return response()->json($result);
    }
    
    public function searchbynamefaculty(Request $request)
    {
        $result = DB::select(DB::raw("select s.*, f.* from staff s, department d, faculty f
                                            where s.departmentId = d.departmentId
                                            and d.facultyId = f.facultyId
                                            and s.staffName like :name
                                            and f.facultyId like :faculty
                                            order by s.staffName;"),array('name'=>"%".$request->name."%", 'faculty'=>"%".$request->facultyId."%"));
        return response()->json($result);
    }
    
    public function addfadminpage()
    {
        $staffresult = DB::select(DB::raw("select s.*, f.* from staff s, department d, faculty f
                                            where s.departmentId = d.departmentId
                                            and d.facultyId = f.facultyId
                                            order by s.staffName;"));
        $facultyresult = Faculty::all();
        return view('AddFadmin',compact('staffresult','facultyresult'));
    }
    
    public function addfyppage()
    {
        $staffresult = DB::select(DB::raw("select s.*, f.* from staff s, department d, faculty f
                                            where s.departmentId = d.departmentId
                                            and d.facultyId = f.facultyId
                                            order by s.staffName;"));
        $facultyresult = Faculty::all();
        return view('AddFYP',compact('staffresult','facultyresult'));
    }
    
    public function deactivatestaffpage()
    {
        $stafffaculty= DB::select(DB::raw("select s.*, f.* from staff s, department d, faculty f
                                            where s.departmentId = d.departmentId
                                            and d.facultyId = f.facultyId
                                            and s.staffId = :staffId;"),array('staffId'=>Session::get('staffId')));
        foreach($stafffaculty as $sf)
        {
            $staffresult = DB::select(DB::raw("select s.*, f.* from staff s, department d, faculty f
                                                where s.departmentId = d.departmentId
                                                and d.facultyId = f.facultyId
                                                and f.facultyId = :facultyId
                                                order by s.staffName;"),array('facultyId'=>$sf->facultyId));
        }
        $staffresult2 = DB::select(DB::raw("select s.*, f.* from staff s, department d, faculty f
                                                where s.departmentId = d.departmentId
                                                and d.facultyId = f.facultyId;"));
        $facultyresult = Faculty::all();
        return view('DeactivateStaff', compact('stafffaculty','staffresult','staffresult2','facultyresult'));
    }
    
    public function updatestaffrole_fadmin(Request $request)
    {		
        $removearr = $request->removearr;
        $addarr = $request->addarr;
        if($removearr !=null)
        {
            for($i = 0 ;$i<sizeof($removearr);$i++)
            {
                $result = Staff::where('staffId','=',$removearr[$i])->first();
                $result->role = "supervisor";
                $result->save();
            }
        }
        
        if($addarr !=null)
        {
            for($i = 0 ;$i<sizeof($addarr);$i++)
            {
                $result2 = Staff::where('staffId','=',$addarr[$i])->first();
                $result2->role = "facultyadmin";
                $result2->save();
            }
        }
        
        return response()->json('The changes have been saved.');
    }
    
    public function updatestaffrole_fyp(Request $request)
    {
        $removearr = $request->removearr;
        $addarr = $request->addarr;
        if($removearr !=null)
        {
            for($i = 0 ;$i<sizeof($removearr);$i++)
            {
                $result = Staff::where('staffId','=',$removearr[$i])->first();
                $result->role = "supervisor";
                $result->save();
            }
        }
        
        if($addarr !=null)
        {
            for($i = 0 ;$i<sizeof($addarr);$i++)
            {
                $result2 = Staff::where('staffId','=',$addarr[$i])->first();
                $result2->role = "fypcommittee";
                $result2->save();
            }
        }
        
        return response()->json(['result'=>"The changes have been saved"]);
    }
    
    public function import(Request $request)
    {
        //validate the xls file
        $this->validate($request, array(
            'file'      => 'required'
        ));
 
        if($request->hasFile('file')){
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {                
 
                    $collection = (new FastExcel)->import($request->file,function($line)
                    {
                        if(!empty($line['staffId']))
                        {
                            try
                            {
                            return Staff::create([
                                'staffId' => $line['staffid'],
                                'title'=> $line['title'],
                                'staffName' => $line['staffname'],
                                'status' => 'active',
                                'role'=>'lecturer',
                                'email' => $line['email'],
                                'departmentId'=>$line['departmentid'],
                            ]);
                            }
                            catch(\Illuminate\Database\QueryException $ex)
                            {
                                $this->errmsg .= $ex->getMessage()."<br>";
                            }
                            catch(\ErrorException $e)
                            {
                                if(empty($this->errmsg))
                                {
                                    $this->errmsg .= $e->getMessage()."<br>";
                                }
                            }
                        }
                    });

                    if (empty($this->errmsg)) {
                        Session::flash('success', 'Your Data has successfully imported');
                    }else {                        
                        Session::flash('error', $this->msgheader.$this->errmsg);
                        return back();
                    }
                    return back();
 
            }else {
                Session::flash('error', 'File is a '.$extension.' file.!! Please upload a valid xls/csv file..!!');
                return back();
            }
        }
    }
    
    public function addnewstaffpage()
    {
        $stafffaculty= DB::select(DB::raw("select f.facultyId from staff s, department d, faculty f
                                            where s.departmentId = d.departmentId
                                            and d.facultyId = f.facultyId
                                            and s.staffId = :staffId;"),array('staffId'=>Session::get('staffId')));
        $departmentresult = Department::where('facultyId','=',$stafffaculty[0]->facultyId)->get();
        return view('AddNewStaff',compact('departmentresult'));
    }
    
    public function addnewstaff(Request $request)
    {
        \error_log('got here');
        $checkid = Staff::where('staffId','=',$request->staffId)->first();
        \error_log($checkid);
        if(!$checkid)
        {
            $staff = Staff::create([
               'staffId'=>$request->get('staffId'),
               'title'=>$request->get('title'),
               'staffName'=>$request->get('staffName'),
               'phoneNo'=>$request->get('phoneNo'),
               'email'=>$request->get('email'),
               'status'=>'active',
               'role'=>'lecturer',
               'full\part'=>$request->get('time'),
               'designation'=>$request->get('designation'),
               'specilization'=>$request->get('specilaization'),
               'departmentId' => $request->get('department')
            ]);
            Session::flash('success', 'New staff was created.');
            return back();
        }
        else
        {
            Session::flash('error', 'This staff Id had been use.');
            return back();
        }
        
    }
    
    public function staffprofilepage()
    {
        $staff = Staff::find(Session::get('staffId'));
        return view('StaffProfile',compact('staff'));
    }
    
    public function updateprofile(Request $request)
    {
        $staff = Staff::find(Session::get('staffId'));
        $staff->title = $request->title;
        $staff->staffName = $request->staffName;
        $staff->phoneNo = $request->phoneNo;
        $staff->designation = $request->designation;
        $staff->specialization = $request->specialization;
        $staff->save();
        
        return response()->json("The changes have been saved.");
    }
    
    //Tee Ren MIan
    public function ViewAllStaff($supervisor_cohort)
    {
        $staff = staff::all()->where('staffId', $supervisor_cohort);
        return $staff;
    }


}
