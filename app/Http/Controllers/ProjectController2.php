<?php

namespace App\Http\Controllers;

use App\Collaborator;
use App\Student;
use Gitonomy\Git\Admin;
use Gitonomy\Git\Exception\ReferenceNotFoundException;
use Gitonomy\Git\Exception\RuntimeException;
use Gitonomy\Git\Parser\TreeParser;
use Gitonomy\Git\Tree;
use GitWrapper\GitException;
use Illuminate\Database\QueryException;
use \Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use \Auth;
use App\Staff;
use App\Project;
use App\Repository;
use App\Team;
use GitWrapper\GitWrapper;
use Gitonomy\Git\Admin as Gito;
use Gitonomy\Git\Repository as GitoRepo;
use SSX\SSH\KeyPair;
use \App\Repository as RepoModel;
use Symfony\Component\Process\Process;
use \App\Ssh_Keys;


class ProjectController2 extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:staff');
    }

    public function repository($id) {// show repository for a certain project
        //$staff = Staff::where('user_id', auth()->user()->staffId)->first();
        $staff = auth()->user()->staffId;
		//if($staff == null) {
        //    return redirect('/');
        //}
        $repository = Project::find($id)->repositories;// find all repository associated with project code
        return view('project.repositorylist', compact('repository'));
    }

    public function formcreaterepository() {
        return view('project.addrepository');
    }

    public function createrepository(Request $request) {

        //if(Staff::find(Auth::user()->id)) {
		//if(auth()->user()->staffId) { 
            $messages = [
                'repositoryname.regex' => 'Repository name may contain only alphanumeric and underscore characters (0-9, a-z, _)',
            ];

            $validatedData = $request->validate([
                'repositoryname' => 'regex:/^[A-Za-z0-9_]*$/|max:255'
            ], $messages);

            try {
                $repository = new Repository();
                $repository->name = $request->input('repositoryname');
                $repository->description = $request->input('repositorydesc');
                $repository->projectCode = $request->input('projectcode');
                $repository->created_by = auth()->user()->staffId;
                $repository->save();

                $this->generateGitConf();
				
				// initialize repository
				$gitLocation = escapeshellarg('c:\Program Files\Git\cmd\git.exe');
				$endLine = '2>&1';
				
				shell_exec($gitLocation . ' clone ' . 'git@i2hub.tarc.edu.my:' . $request->input('repositoryname') . ' ' . "C:/Users/Administrator/Desktop/adminFolder/".$request->input('repositoryname') .' '. $endLine);
				chdir("C:/Users/Administrator/Desktop/adminFolder/".$request->input('repositoryname'));
				file_put_contents("Welcome.txt", "Hello and welcome to ".$request->input('repositoryname'));
				shell_exec($gitLocation . " config user.email ". escapeshellarg(auth()->user()->email) . $endLine);
				shell_exec($gitLocation . " config user.name " . escapeshellarg(auth()->user()->staffName) . $endLine);
				shell_exec($gitLocation. ' add Welcome.txt '.$endLine);
				shell_exec($gitLocation. ' commit -m ' . escapeshellarg(auth()->user()->staffId) . ' ' . $endLine);
				shell_exec($gitLocation. ' push '.$endLine);
				
				
				
                // redirect to project repository list with success msg
                return redirect()
                    ->route('project_repository', $request->input('projectcode'))
                    ->with('success', 'New repository called '. $request->input('repositoryname') . ' has been created');
            }
            catch (QueryException $e) {
                dd($e->getMessage());
                // redirect to (create repository) with failed msg
                return redirect()
                    ->route('form_create_repository', $request->input('projectcode'))
                    ->with('failed', 'Repository with specific name already exists!');
            }
        //}
       // else {
            //return redirect()->route('project_home')->with('notsupervisor', 'Sorry, only supervisor can add repository');
       // }
    }

    public function formassignteam() {
        //$staff_id = auth()->user()->staffId; // get staff id
        //$teams = Team::where('supervisor', $staff_id)->get();// get team related to staff_id
        //dd($teams);
        //$projects = Project::where('advisor', $staff_id)->get();// returns collection of all projects under staff :) had to modify in order to integrate
        //dd($projects);
		//$project_codes = array();
       // foreach($projects as $project){
       //     array_push($project_codes, $project->project_code);
       // }
        //dd($project_codes);
        //return view('project.addteam', compact('teams','project_codes'));
    }

    public function formaddstudenttorepo($id) {
        // if student team belongs to the project selected
        $staff_id = auth()->user()->staffId; // get staff id
        $teams = Team::where('supervisor',$staff_id)->where('projectCode', $id)->get();

        return view('project.addstudent', compact('teams'));
    }

    public function addstudenttorepo(Request $request) {

        $students = $request->input('students');
        $assigned = $request->input('assigned');
        $repo_id = $request->input('repositoryid');

        if($students==null)
            return redirect()->back()->with('error', 'No students available');

        // if staff has access to add student
        $repo_creator = Repository::find($repo_id)->created_by;
        if(auth()->user()->staffId != $repo_creator) {
            return redirect()->route('projectlist.viewprojectlists')->with('error', 'Not authorized');
        }

        foreach($students as $s) {
            $already_assigned = Collaborator::where('studentId', $s)->where('repository_id', $repo_id);
            // check if array is valid
            if(!empty($assigned)) {
                if(in_array($s, $assigned)) {
                    // check if already in DB, if yes then do nothing lmao
                    if($already_assigned->get()->isEmpty()) {
                        // then only add if not in DB
                        $collab = new Collaborator();
                        $collab->repository_id =  $repo_id;// need some validation here!
                        $collab->studentId = $s;
                        $collab->save();// add into DB
                    }
                }
                else {
                    // if have in DB then find and delete
                    if(!$already_assigned->get()->isEmpty())
                        $already_assigned->delete();
                }
            }
            // if array is not valid then un-assign all students
            else  {
                if(!$already_assigned->get()->isEmpty())
                    $already_assigned->delete();
            }
        }

        $this->generateGitConf();

        return redirect()->back()->with('success', 'Successfully modified table');// success message needs to be more meaningful
    }

    public function displayrepository($id, $br = 'master')
    {
        // FILE STRUCTURE
        // check if staff has access!

        $repo = RepoModel::find($id);
        if(auth()->user()->staffId != $repo->created_by) {
            return redirect()
                ->route('projectlist.viewprojectlists')
                ->with('error', 'Do not have access');
        }

		$gitLocation = escapeshellarg('c:\Program Files\Git\cmd\git.exe');
		$endLine = '2>&1';
		
		$cmd1 = shell_exec($gitLocation . ' clone ' . 'git@i2hub.tarc.edu.my:' . $repo->name . ' ' . "C:/Users/Administrator/Desktop/adminFolder/".$repo->name .' '. $endLine);
		if(strpos($cmd1, "already exists and is not an empty directory.") !== false){
			chdir("C:\Users\Administrator\Desktop\adminFolder\\".$repo->name);
			shell_exec($gitLocation . ' pull ' . $endLine);
		}

        $repository = new \Gitonomy\Git\Repository("C:/Users/Administrator/Desktop/adminFolder/".$repo->name);
		//dd($repository);
        try {
            $branch = $repository->getReferences()->getBranch($br); // add error handling when branch not exists
        }catch (ReferenceNotFoundException $e) {
            return redirect()->back()->with('error', 'Repository '. $repo->name.' is empty, add some files first.');
            //dd($e);
        }
        $tree = $branch->getCommit()->getTree();
        $arr = $this->displayTree($tree);

        // BRANCH
        $all_branch_local = $repository->getReferences()->getLocalBranches();


        // TAGS
        $returnTags=array();

        $tags = $repository->getReferences()->getTags();
        //dd($tags);
        foreach($tags as $t) {
            //dd($references->getTag('v1.0')->getCommit()->getMessage());
            $ta = $repository->run('tag', array('-l','--format=%(contents),%(taggerdate),%(taggeremail)','--sort=-taggerdate', $t->getName()));
            $string = trim(preg_replace('/\s\s+/', ' ', $ta)); // remove \n
            //$returnTags = explode(',', $string);
            array_push($returnTags, explode(',', $string));
            //dd($ta);
        }
		
        return view('project.repository', compact('arr', 'all_branch_local', 'returnTags'));
    }
	
	public function addRepoTags(Request $request){
		// future enhancement: check if a person has access to repository 
		
		$repository_name = $request->input('reponame');
		$version = $request->input('tagversion');
		$desc = $request->input('tagdesc');
		$repo = Repository::where('name', $repository_name)->first();
		//dd($repo);
		$gitLocation = escapeshellarg('c:\Program Files\Git\cmd\git.exe');
		$endLine = '2>&1';
				
		$cmd1 = shell_exec($gitLocation . ' clone ' . 'git@i2hub.tarc.edu.my:' . $repo->name . ' ' . "C:/Users/Administrator/Desktop/adminFolder/".$repo->name .' '. $endLine);
		if(strpos($cmd1, "already exists and is not an empty directory.") !== false){
			chdir("C:\Users\Administrator\Desktop\adminFolder\\".$repo->name);
			shell_exec($gitLocation . ' pull ' . $endLine);
		}
		chdir("C:\Users\Administrator\Desktop\adminFolder\\".$repo->name);
		shell_exec($gitLocation . " config user.email ". escapeshellarg(auth()->user()->email) . $endLine);
		shell_exec($gitLocation . " config user.name " . escapeshellarg(auth()->user()->staffName) . $endLine);
		shell_exec($gitLocation. " tag -a " . escapeshellarg($version) . " -m " . escapeshellarg($desc) . " " . $endLine);
		shell_exec($gitLocation. " push --tags " . $endLine);
		
		//dd('hello');
		return redirect()->route('project_display_repository', ['id'=>$repo->id, 'br'=>'master']);
	}

    // Helper
    function displayTree(Tree $tree) {
        $arr=array();
        foreach($tree->getEntries() as $name=>$data){
            if($data[1] instanceof Tree) {
                $files = $this->displayTree($data[1]);
                array_push($arr, array('text'=>$name, 'children'=>$files));
            }
            else {
					array_push($arr,array('text'=>$name, 'data'=>array('blob'=>$data[1]->getHash())));
				
				
            }
        }
        return $arr;
    }

    
    /**
     * Add user public keys to keydir folder in gitolite
     * Important, so that user can push and pull from the git repository
     * Only used when importing students from the main database
     */

    public function addKeys() {
		// new - not efficient but gets the job done
		$gitLocation = escapeshellarg('c:\Program Files\Git\cmd\git.exe');
		$endLine = '2>&1';
		
		$cmd1 = shell_exec($gitLocation.' clone git@i2hub.tarc.edu.my:gitolite-admin c:\users\Administrator\Desktop\adminFolder\gitolite-admin '.$endLine);
		if(strpos($cmd1, "already exists and is not an empty directory.") !== false){
			chdir("C:\Users\Administrator\Desktop\adminFolder\gitolite-admin");
			shell_exec($gitLocation . ' pull ' . $endLine);
		}
		chdir("C:\Users\Administrator\Desktop\adminFolder\gitolite-admin");
		$students = Student::all();
        $staffs = Staff::all();
		$keypair = new KeyPair();
        
		foreach($students as $student ) {
			//dd(Student::where('studentId', '17WMR09588')->first()->priv_key->privateKey);
			if($student->priv_key == null){
				$ssh = new Ssh_Keys();
				$ssh->userId = $student->studentId;
				$ssh->privateKey = $keypair->getPrivateKey();
				$ssh->publicKey = $keypair->getPublicKey();
				file_put_contents('keydir/'.$student->studentId.'.pub', $ssh->publicKey);
				$ssh->save();
			}
			else{
				file_put_contents('keydir/'.$student->studentId.'.pub', $student->priv_key->publicKey);
			}
            
        }
		
		foreach($staffs as $staff ) {
			//dd(Student::where('studentId', '17WMR09588')->first()->priv_key->privateKey);
			if($staff->priv_key == null){
				$ssh = new Ssh_Keys();
				$ssh->userId = $staff->staffId;
				$ssh->privateKey = $keypair->getPrivateKey();
				$ssh->publicKey = $keypair->getPublicKey();
				file_put_contents('keydir/'.$staff->staffId.'.pub', $ssh->publicKey);
				$ssh->save();
			}
			else{
				file_put_contents('keydir/'.$staff->staffId.'.pub', $staff->priv_key->publicKey);
			}   
        }
		
		shell_exec($gitLocation . " config user.email ". escapeshellarg(auth()->user()->email) . $endLine);
		shell_exec($gitLocation . " config user.name " . escapeshellarg(auth()->user()->staffName) . $endLine);
		shell_exec($gitLocation. ' pull '.$endLine);
		shell_exec($gitLocation. ' add . '.$endLine);
		shell_exec($gitLocation. ' commit -m ' . auth()->user()->staffId . ' ' . $endLine);
		shell_exec($gitLocation. ' push '.$endLine);
		
		dd('Success');
		
    }

    /**
     * Generate gitolite.conf which controls access to repositories
     */

    public function generateGitConf() {
        //$git = $this->getWorkingCopy();

        // part 1: add team composition to gitolite.conf
        $all_teams = Team::all();
		
        $lines = null;
        foreach($all_teams as $team) {
            $students = $team->students;
            //dd($students);
            if(!$students->isEmpty()) {
                $lines .= '@team_' . $team->teamId . ' = ';
                foreach($students as $stud ) {
                    $lines .= $stud->studentId . ' ';
                }
                $lines .= PHP_EOL;
            }
        }
        $lines .= PHP_EOL;
        //dd($lines);

        $lines .= '# Gitolite admin and testing repo'.PHP_EOL;
        $lines .= 'repo gitolite-admin'.PHP_EOL;
        $lines .= '    RW+ = gito'.PHP_EOL;
        $lines .= 'repo testing'.PHP_EOL;
        $lines .= '    RW+ = @all'.PHP_EOL.PHP_EOL;

        // part 2: add repo
        $all_projects = Project::all();
        //dd($all_projects);
        foreach($all_projects as $project ) {
            $repos = $project->repositories;
            //dd($repos);
            $teams = $project->teams;
            //dd($teams);
            foreach ($repos as $repo) {
                $lines .= 'repo ' . $repo->name . PHP_EOL;

                if (!$teams->isEmpty()) {
                    $lines .= '    R = ';
                    foreach($teams as $team) {
                        $lines .= '@team_' . $team->teamId . ' ';
                    }
                    $lines .= PHP_EOL;
                }

                $collabs = $repo->collaborators;
                if(!$collabs->isEmpty()) {
                    $lines .= '    RW+ = ';
                    foreach($collabs as $c) {
                        $lines .= $c->studentId . ' ';
                    }
                    // staff also need write access
                    $lines .= PHP_EOL;
                }
				//dd($repo->staff()->get());
                $lines .= '    RW+ = '.$repo->staff()->first()->staffId.PHP_EOL;
                $lines .= '    RW+ = gito'.PHP_EOL;
                $lines .= PHP_EOL;
            }
        }
        $gitLocation = escapeshellarg('c:\Program Files\Git\cmd\git.exe');
		$endLine = '2>&1';
		
        // Part 3 : push updates
		
		$cmd1 = shell_exec($gitLocation.' clone git@i2hub.tarc.edu.my:gitolite-admin c:\users\Administrator\Desktop\adminFolder\gitolite-admin '.$endLine);
		if(strpos($cmd1, "already exists and is not an empty directory.") !== false){
			chdir("C:\Users\Administrator\Desktop\adminFolder\gitolite-admin");
			shell_exec($gitLocation . ' pull ' . $endLine);
		}
		chdir("C:\Users\Administrator\Desktop\adminFolder\gitolite-admin");
		file_put_contents('conf/gitolite.conf', $lines);
		shell_exec($gitLocation . " config user.email ". escapeshellarg(auth()->user()->email) . $endLine);
		shell_exec($gitLocation . " config user.name " . escapeshellarg(auth()->user()->staffName) . $endLine);
		shell_exec($gitLocation. ' pull '.$endLine);
		shell_exec($gitLocation. ' add conf/gitolite.conf '.$endLine);
		shell_exec($gitLocation. ' commit -m ' . auth()->user()->staffId . ' ' . $endLine);
		shell_exec($gitLocation. ' push '.$endLine);
	}
}
