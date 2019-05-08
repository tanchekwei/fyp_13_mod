<?php

namespace App\Http\Controllers;

use App\Collaborator;
use App\Student;
use App\Staff;
use App\Team;
use Gitonomy\Git\Blob;
use Gitonomy\Git\Exception\ReferenceNotFoundException;
use Gitonomy\Git\Tree;
use Illuminate\Http\Request;
//use Gitonomy\Git\Repository as Gito;
use Gitonomy\Git\Admin as Gito;
use \App\Repository as RepoModel;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\File;
use Session;

class StudentController2 extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    public function index()
    {
        //$student = Student::where('user_id', Auth::user()->id)->get();// Get student id
		$student = Student::find(Session::get('studentId'));
		//dd($student->studentId);
		$project_code = Team::find($student->teamId)->projectCode;
        
        // Display all repository student has access
        $all_student_repo = RepoModel::where('projectCode', $project_code)->get();
        
        return view('student.index', compact('all_student_repo'));
    }

    public function displayrepository($id, $br = 'master')
    {
        
        $student = Student::find(Session::get('studentId'));// Get student id
		//dd($student->get());
        $project_code = Team::find($student->teamId)->projectCode;
		
        $all_student_repo = RepoModel::where('projectCode', $project_code)->get();
//dd($all_student_repo);
        if(!$all_student_repo->contains('id',$id)){

            return redirect()
                ->route('student_home')
                ->with('status', 'Do not have access');
        }


        $repo = RepoModel::find($id);
        $gitLocation = escapeshellarg('c:\Program Files\Git\cmd\git.exe');
		$endLine = '2>&1';
		//dd('‪C:/Users/Administrator/Desktop/adminFolder/'.$repo->name);
		$cmd1 = shell_exec($gitLocation . ' clone ' . 'git@i2hub.tarc.edu.my:' . $repo->name . ' ' . "C:/Users/Administrator/Desktop/adminFolder/".$repo->name .' '. $endLine);
		if(strpos($cmd1, "already exists and is not an empty directory.") !== false){
			chdir("C:\Users\Administrator\Desktop\adminFolder\\".$repo->name);
			shell_exec($gitLocation . ' pull ' . $endLine);
		}

        $repository = new \Gitonomy\Git\Repository("C:/Users/Administrator/Desktop/adminFolder/".$repo->name);
        
        $references = $repository->getReferences();
        
        try {
            $branch = $repository->getReferences()->getBranch($br); // add error handling when branch not exists
        }catch (ReferenceNotFoundException $e) {
            return redirect()->back()->with('error', 'Repository '. $repo->name.' is empty, please initialize it.');
            
        }
        $tree = $branch->getCommit()->getTree();
        $arr = $this->displayTree($tree);


        // BRANCH
        $all_branch_local = $references->getLocalBranches();


        // TAGS
        $returnTags=array();

        $tags = $references->getTags();
        foreach($tags as $t) {
            
            $ta = $repository->run('tag', array('-l','--format=%(contents),%(taggerdate),%(taggeremail)','--sort=-taggerdate', $t->getName()));
            $string = trim(preg_replace('/\s\s+/', ' ', $ta)); // remove \n
            
            array_push($returnTags, explode(',', $string));
            
        }
        return view('student.repository', compact('arr', 'all_branch_local', 'returnTags'));
    }

    //// Helper

    function displayTree(Tree $tree) {
        $arr=array();
        foreach($tree->getEntries() as $name=>$data){
            if($data[1] instanceof Tree) {
                $files = $this->displayTree($data[1]);
                array_push($arr, array('text'=>$name, 'children'=>$files));
            }
            else {

                array_push($arr,array('text'=>$name, 'data'=>array('blob'=>$data[1]->getHash()) ));
            }
        }
        return $arr;
    }

}
