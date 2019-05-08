<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Staff;
use App\Team;
use App\Supervisor_Cohort;
use App\Student;
use DB;
use Session;
use Excel;
use File;

class TeamController extends Controller
{
    private $msgheader="There have the error(s) when importing the file.<br> "
            . "The error might be:<br> "
            . "Incorrect table column name in excel file.<br>"
            . "dulpicate from database or missing the Team Id/project Code.<br>"
            . "The error(s) detail display as below:<br>";
    
    private $errmsg;

    public function __construct() 
    {
        $this->middleware('auth:staff');
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
                        if(!empty($line['teamId']))
                        {
                            try
                                {
                            $haveteam = Team::find($line['teamId']);
                            $supervisor = Staff::where('staffName','like','%'.$line['supervisor'].'%')->first();
                            $moderator = Supervisor_Cohort::where('staffId','=',$supervisor->staffId)->where('cohortId','=',Session::get('cohortId'))->first();
                            if(empty($haveteam))
                            {
                                
                                return Team::create([
                                    'teamId' => $line['teamid'],
                                    'supervisor' =>$supervisor->staffId,
                                    'moderator' => $moderator->moderatorId,
                                    'competitionName' => $line['competitionname'],
                                    'status'=>'assigned',
                                    'projectCode' => $line['projectcode'],                                    
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
                        }
                    });
                $collection2 = (new FastExcel)->import($request->file,function($line)
                {
                    if(!empty($line['teamId']))
                    {
                        try
                        {
                        $student = Student::find($line['studentid']);
						if(!empty($student))
						{
                        $student->teamId = $line['teamid'];
                        $student->individualTitle = $line['individualtitle'];
                        $student->save();
                        return $student;
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
                Session::flash('error', 'File is a '.$extension.' file.!! Please upload a valid xls/xlsx file..!!');
                return back();
            }
        }
    }
}
