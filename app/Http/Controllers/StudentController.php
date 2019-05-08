<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Student;
use DB;
use Session;
use Excel;
use File;

class StudentController extends Controller
{
    private $prog;
    private $msgheader="There have the error(s) when importing the file.<br> "
            . "The error might be:<br> "
            . "Incorrect table column name in excel file.<br>"
            . "dulpicate from database or missing the project Code.<br>"
            . "The error(s) detail display as below:<br>";
    
    private $errmsg;

    public function __construct() 
    {
        $this->middleware('auth:staff,student');
    }

    public function setprog($prog)
    {
        $this->prog = $prog;
    }
    
    public function getprog()
    {
        return $this->prog;
    }
    
    public function import(Request $request)
    {
        //validate the xls file
        $this->validate($request, array(
            'file'      => 'required',
            'level'     => 'required'
        ));
        $this->setprog($request->level);
       
        if($request->hasFile('file')){
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {                 
 
                $collection = (new FastExcel)->import($request->file,function($line)
                {
                    try
                    {
						$student = Student::find($line['studentid']);
						if(empty($student))
						{
                    return Student::create([
                        'studentId'=>$line['studentid'],
                        'studentName'=>$line['studentname'],
                        'TARCemail'=>$line['tarcemail'],
                        'status'=>'active',
                        'tutorialGroup'=>$line['tutorialgroup'],
                        'cohortId'=>Session::get('cohortId'),
                        'programmeId'=>$this->getprog().$line['programmeid']
                    ]);
						}
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
    
    public function studentprofilepage()
    {
        $student = Student::find(Session::get('studentId'));
        return view('StudentProfile',compact('student'));
    }
    
    public function updateprofile(Request $request)
    {
        $student = Student::find(Session::get('studentId'));
        $student->phoneNo = $request->phoneNo;
        $student->save();
        
        return response()->json("The changes have been saved.");
    }
}
