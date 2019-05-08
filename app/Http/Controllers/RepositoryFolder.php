<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\Staff;
use \App\Repository;
use File;
class RepositoryFolder extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:staff,student');
    }
	
	public function viewFile($id, $branch, $hash) {

        $student = Student::where('studentId', auth()->user()->studentId)->first();
		//dd($student);
        $staff = Staff::where('staffId', auth()->user()->staffId)->first();
        $repo = Repository::find($id);

        if($student != null || $staff != null) {
            if($student != null) {
				//dd($student->team()->first()->projectCode);
                if($student->team()->first()->projectCode == $repo->projectCode) {
                    // has view access
                    if(!File::exists('C:/Users/Administrator/Desktop/adminFolder/'.$repo->name)) {
                        return redirect()->back()->with('error', 'Please access properly.');
                    }
                    $repository = new \Gitonomy\Git\Repository('C:/Users/Administrator/Desktop/adminFolder/'.$repo->name);
                    //$repository=new \Gitonomy\Git\Repository('C:\Users\Laurrence\Desktop\git_sample_folder');
                    $blob = $repository->getBlob($hash)->getContent();
                    return view('viewfile', compact('blob'));
                }
                else {
                    //redirect
                }
            }
            else {
                if($staff->staffId == $repo->staff()->first()->staffId) {
                    if(!File::exists('C:/Users/Administrator/Desktop/adminFolder/'.$repo->name)) {
                        return redirect()->back()->with('error', 'Please access properly.');
                    }
                    $repository = new \Gitonomy\Git\Repository('C:/Users/Administrator/Desktop/adminFolder/'.$repo->name);
                    //$repository=new \Gitonomy\Git\Repository(storage_path().'/git_sample_folder/');
                    $blob = $repository->getBlob($hash)->getContent();
                    return view('viewfile', compact('blob'));
                }
                else {
                    //redirect
                }
            }
        }
        else {
            return redirect('/'); // put error here "user not logged in"
        }
    }
}
