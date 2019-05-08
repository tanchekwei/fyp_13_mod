<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\cohort;
use App\DeliverableOld;
use Illuminate\Support\Facades\Input;
use App\Cohort_deliverable;
use App\Deliverable;
use Session;

class ManageDeliverableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:staff');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cohorts = DB::table('Cohort')->select('cohortId')->orderBy('cohortId')->get();
        $faculty = DB::table('Faculty')->select('facultyId','facultyName')->get();

        $data = array();
        $data['faculties'] = $faculty;
        $data['cohorts'] = $cohorts;

        $staff = DB::table('staff')->select('role')->where('staffId', Auth::user()->staffId)->get();
        //check if page is being accessed by FYPcommittee or admin
        if($staff[0]->role == "admin" || $staff[0]->role == "fypcommittee")
			return view('pages.manage_deliverable.manage_deliverable', compact("data"));
        else
			return view("index");
    }

    /*
    public function ajax_load_programme()
    {
        $faculty = INPUT::get('faculty');
        $programmes = DB::table('Faculty')->select('programmes.programmeName')->
        join('programmes', 'programmes.facultyID', '=', 'faculties.facultyID')->
        where('faculties.facultyName', '=', $faculty)->get();

        return json_encode($programmes);
        //
    }
    */

    public function ajax_load_deliverable()
    {
        $cohort = INPUT::get('cohort');

        $faculty = INPUT::get('faculty');
        $facultyID = DB::table('faculty')->select('facultyId')->where('facultyId', $faculty)->get();
        $facultyID = $facultyID[0]->facultyId;

        //$programme = INPUT::get('programme');
        //$programmeID = DB::table('programmes')->select('programmeID')->where('programmeName', $programme)->get();
        //$programmeID = $programmeID[0]->programmeID;

        $isCompetition = 0;
        if (INPUT::get('competition') == "Normal")
            $isCompetition = 0;
        else
            $isCompetition = 1;

        $cohort_deliverable_id = DB::table('Cohort_Deliverable')->select('cohort_deliverable_id')->where([
            ['cohortID', '=', $cohort],
            ['facultyID', '=', $facultyID],
            //['programmeID', '=', $programmeID],
            ['isCompetition', '=', $isCompetition]
        ])->get();

        if ($cohort_deliverable_id->isNotEmpty()) {
            $cohort_deliverable_id = $cohort_deliverable_id[0]->cohort_deliverable_id;
            $deliverables = DB::table('Deliverable')->
            select('Deliverable.deliverable_id', 'Deliverable.deliverable_name', 'Deliverable.showcase', 'Deliverable.deliverable_type_id', 'deliverable_type.deliverable_type', 'deliverable_type.deliverable_extension')->
            join('Deliverable_Type', 'Deliverable.deliverable_type_id', '=', 'Deliverable_Type.deliverable_type_id')->
            where('Deliverable.cohort_deliverable_id', '=', $cohort_deliverable_id)->get();

            if($deliverables->isNotEmpty()) {

                $available_deliverable_types = DB::table('Deliverable_Type')->select('deliverable_type_id', 'deliverable_type')->get();

                //return table
                $i = 0;
                foreach ($deliverables as $deliverable) {
                    echo "<tr id = 'row" . $i . "'>";
                    echo "<td><input type = 'text' value = '" . $deliverable->deliverable_name . "' name = 'name[]' id='" . $deliverable->deliverable_id . "' placeholder = 'Field name' class='form-control' required/></td >";
                    echo "<td ><select class='selected_deliverable custom-select' name='deliverable_type[]'>";
                    foreach ($available_deliverable_types as $available_deliverable_type) {
                        if ($available_deliverable_type->deliverable_type_id == $deliverable->deliverable_type_id)
                            echo "<option id = '" . $available_deliverable_type->deliverable_type_id . "' selected >" . $available_deliverable_type->deliverable_type . "</option >";
                        else
                            echo "<option id = '" . $available_deliverable_type->deliverable_type_id . "'>" . $available_deliverable_type->deliverable_type . "</option >";
                    }
                    echo "</select></td >";
                    echo "<td class='selected_deliverable_extension'><label class='col-form-label'>" . $deliverable->deliverable_extension . "</label></td >";
                    echo "<td ><select class='selected_deliverable custom-select' name='showcase[]'>";
                    if ($deliverable->showcase == "0") {
                        echo "<option >True</option >";
                        echo "<option selected>False</option >";
                    } else {
                        echo "<option selected>True</option >";
                        echo "<option>False</option >";
                    }
                    echo "</select></td >";
                    echo "<td ><button class='btn btn-danger btn_remove' type = 'button' name = 'remove' id = '" . $i . "' >Delete</button ></td >";
                    echo "</tr >";
                    $i++;
                }

                echo "<tr>";
                echo "<td><button class='btn btn-dark btn_add'>Add</button></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td><input class='btn btn-success btn_submit' type='submit' value='Save'></td>";
                echo "</tr>";
                return;
            }
            else{
                echo "<div id='empty_message'>";
                echo "<td>No deliverable was found. Do you wish to replicate the deliverable from last cohort?</td>";
                echo "<td>";
                echo "<input type='button' id='btn_no' class='btn-danger' value='No' name='No'/>&nbsp;&nbsp;&nbsp;&nbsp;";
                echo "<input type='button' id='btn_yes' class='btn-success' value='Yes' name='yes'/>";
                echo "</td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";
            }
        }
        else{
            echo "<div id='empty_message'>";
            echo "<td>No deliverable was found. Do you wish to replicate the deliverable from last cohort?</td>";
            echo "<td>";
            echo "<input type='button' id='btn_no' class='btn-danger' value='No' name='No'/>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "<input type='button' id='btn_yes' class='btn-success' value='Yes' name='yes'/>";
            echo "</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "</tr>";
        }

    }

    public function ajax_add_deliverable()
    {
        $index = INPUT::get('index');
        $available_deliverable_types = DB::table('Deliverable_Type')->select('deliverable_type_id', 'deliverable_type', 'deliverable_extension')->get();

        echo "<tr id='row" . $index . "'>";
        echo "<td><input type='text' name = 'name[]' class='form-control name_list' id='new' required/></td>";
        echo "<td><select class='selected_deliverable custom-select' name='deliverable_type[]'>";
        $i = 0;
        $first_deliverable_extension = "";
        foreach ($available_deliverable_types as $available_deliverable_type) {
            echo "<option id = '" . $available_deliverable_type->deliverable_type_id . "'>" . $available_deliverable_type->deliverable_type . "</option >";
            if ($i == 0) {
                $first_deliverable_extension = $available_deliverable_type->deliverable_extension;
            }
            $i++;
        }
        echo "</select></td>";
        echo "<td class='selected_deliverable_extension col-form-label'><label class='col-form-label'>$first_deliverable_extension</label></td>";
        echo "<td ><select class='selected_deliverable custom-select' name='showcase[]'>";
        echo "<option>True</option >";
        echo "<option>False</option >";
        echo "</select ></td >";
        echo "<td><button type=\"button\" name=\"remove\" id=\"${index}\" class=\"btn btn-danger btn_remove\">Delete</button></td>";
        echo "</tr>";
        return;

    }

    public function ajax_change_deliverable_extension()
    {
        $id = INPUT::get('index');
        $available_deliverable_type = DB::table('Deliverable_Type')->select('deliverable_extension')->where('deliverable_type_id', $id)->get();
        return $available_deliverable_type[0]->deliverable_extension;
    }

    public function ajax_copy_yes()
    {
        $cohort = INPUT::get('cohort');
        $faculty = INPUT::get('faculty');
        $facultyID = DB::table('Faculty')->select('facultyID')->where('facultyId', $faculty)->get();
        $facultyID = $facultyID[0]->facultyID;

        //$programme = INPUT::get('programme');
        //$programmeID = DB::table('Programme')->select('programmeID')->where('programmeName', $programme)->get();
        //$programmeID = $programmeID[0]->programmeID;

        $isCompetition = 0;
        if (INPUT::get('competition') == "Normal")
            $isCompetition = 0;
        else
            $isCompetition = 1;

        $cohort_deliverable_id = DB::table('Cohort_Deliverable')->select('cohort_deliverable_id')->where([
            ['cohortID', '<', $cohort],
            ['facultyID', '=', $facultyID],
            //['programmeID', '=', $programmeID],
            ['isCompetition', '=', $isCompetition]
        ])->orderBy('cohortID', 'desc')->get();

        if ($cohort_deliverable_id->isNotEmpty()) {
            $cohort_deliverable_id = $cohort_deliverable_id[0]->cohort_deliverable_id;
            $deliverables = DB::table('Deliverable')->
            select('Deliverable.deliverable_id', 'Deliverable.deliverable_name', 'Deliverable.showcase', 'Deliverable.deliverable_type_id', 'Deliverable_Type.deliverable_type', 'Deliverable_Type.deliverable_extension')->
            join('Deliverable_Type', 'Deliverable.deliverable_type_id', '=', 'Deliverable_Type.deliverable_type_id')->
            where('Deliverable.cohort_deliverable_id', '=', $cohort_deliverable_id)->get();

            $available_deliverable_types = DB::table('Deliverable_Type')->select('deliverable_type_id', 'deliverable_type')->get();

            //return table
            $i = 0;
            foreach ($deliverables as $deliverable) {
                echo "<tr id = 'row" . $i . "'>";
                echo "<td><input type = 'text' value = '" . $deliverable->deliverable_name . "' name = 'name[]' id='new' placeholder = 'Field name' class='form-control' required/></td >";
                echo "<td ><select class='selected_deliverable custom-select' name='deliverable_type[]'>";
                foreach ($available_deliverable_types as $available_deliverable_type) {
                    if ($available_deliverable_type->deliverable_type_id == $deliverable->deliverable_type_id)
                        echo "<option id = '" . $available_deliverable_type->deliverable_type_id . "' selected >" . $available_deliverable_type->deliverable_type . "</option >";
                    else
                        echo "<option id = '" . $available_deliverable_type->deliverable_type_id . "'>" . $available_deliverable_type->deliverable_type . "</option >";
                }
                echo "</select></td >";
                echo "<td class='selected_deliverable_extension'><label class='col-form-label'>" . $deliverable->deliverable_extension . "</label></td >";
                echo "<td ><select class='selected_deliverable custom-select' name='showcase[]'>";
                if ($deliverable->showcase == "0") {
                    echo "<option >True</option >";
                    echo "<option selected>False</option >";
                } else {
                    echo "<option selected>True</option >";
                    echo "<option>False</option >";
                }
                echo "</select></td >";
                echo "<td ><button class='btn btn-danger btn_remove' type = 'button' name = 'remove' id = '" . $i . "' >Delete</button ></td >";
                echo "</tr >";
                $i++;
            }

            echo "<tr>";
            echo "<td><button class='btn btn-dark btn_add'>Add</button></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td><input class='btn btn-success btn_submit' type='submit' value='Save'></td>";
            echo "</tr>";
            return;
        }
        else{
            echo "<tr>";
            echo "<td><button class='btn btn-dark btn_add'>Add</button></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td><input class='btn btn-success btn_submit' type='submit' value='Save'></td>";
            echo "</tr>";
            return;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cohort = $request->get('cohort');

        $faculty = $request->get('faculty');
        $facultyID = DB::table('Faculty')->select('facultyId')->where('facultyId', $faculty)->get();
        $facultyID = $facultyID[0]->facultyId;

        //$programme = $request->get('programme');
        //$programmeID = DB::table('Programme')->select('programmeID')->where('programmeName', $programme)->get();
        //$programmeID = $programmeID[0]->programmeID;

        $isCompetition = 0;
        if (INPUT::get('competition') == "Normal")
            $isCompetition = 0;
        else
            $isCompetition = 1;

        $cohort_deliverable_id = DB::table('Cohort_Deliverable')->select('cohort_deliverable_id')->where([
            ['cohortID', '=', $cohort],
            ['facultyID', '=', $facultyID],
            //['programmeID', '=', $programmeID],
            ['isCompetition', '=', $isCompetition]
        ])->get();

        $deliverable_name = $request->get('name');
        $showcase = $request->get('showcase');

        $this->validate($request,[
            'name.*' => 'required'
            ]);


        //get name and type in delimited string format and convert them to array
        $deliverable_id = $request->get('deliverable_id');
        $deliverable_id_array = explode(",", $deliverable_id);
        $deliverable_type_id = $request->get('deliverable_type_id');
        $deliverable_type_id_array = explode(",", $deliverable_type_id);

        //cohort_deliverable_id exist
        if ($cohort_deliverable_id->isNotEmpty()) {

            //get existing deliverable for this cohort_deliverable
            $existing_deliverable_ids = DB::table('Deliverable')->select('deliverable_id')->where('cohort_deliverable_id', $cohort_deliverable_id[0]->cohort_deliverable_id)->get();

            //FIND ID TO BE DELETED
            $deleted_deliverable_id = array();
            foreach ($existing_deliverable_ids as $existing_deliverable_id) {
                $delete = 1;
                foreach ($deliverable_id_array as $deliverable_id_item) {
                    if ($existing_deliverable_id->deliverable_id == $deliverable_id_item) {
                        $delete = 0;
                    }
                }
                if ($delete == 1) {
                    array_push($deleted_deliverable_id, $existing_deliverable_id->deliverable_id);
                }
            }

            //UNNECESSARY VALIDATION: PREVENT MIS-DELETE OF RECORD CAUSED BY USER CHANGING HTML CODE
            /*
            $error = 0;
            if(sizeof($deleted_deliverable_id) != 0) {
                foreach ($deleted_deliverable_id as $d) {
                    $cohort_deliverable_id_check = DB::table('Deliverable')->select('cohort_deliverable_id')->where('deliverable_id', $d)->get();
                    if ($cohort_deliverable_id_check !== $cohort_deliverable_id) {
                        $error = 1;
                    }
                }
            }
            if($error == 1){
                return redirect("/managedeliverable")->with("error", "Malicious activity was detected!");
            }
            */
            //END OF VALIDATION

            for ($i = 0; $i < sizeOf($deliverable_id_array); $i++) {
                //insert
                if ($deliverable_id_array[$i] == "new") {
                    $deliverable = new Deliverable();
                    $deliverable->deliverable_name = $deliverable_name[$i];
                    if ($showcase[$i] == "True") {
                        $deliverable->showcase = 1;
                    }
                    else {
                        $deliverable->showcase = 0;
                    }
                    $deliverable->cohort_deliverable_id = $cohort_deliverable_id[0]->cohort_deliverable_id;
                    $deliverable->deliverable_type_id = $deliverable_type_id_array[$i];
                    $deliverable->save();
                }
                //update
                else {
                    $deliverable = Deliverable::find($deliverable_id_array[$i]);
                    if ($deliverable) {
                        $deliverable->deliverable_name = $deliverable_name[$i];
                        if ($showcase[$i] == "True") {
                            $deliverable->showcase = 1;
                        }
                        else {
                            $deliverable->showcase = 0;
                        }
                        $deliverable->cohort_deliverable_id = $cohort_deliverable_id[0]->cohort_deliverable_id;
                        $deliverable->deliverable_type_id = $deliverable_type_id_array[$i];
                        $deliverable->save();
                    }
                }
            }
			//delete
                if (sizeof($deleted_deliverable_id) > 0) {
                    foreach ($deleted_deliverable_id as $ddi) {
                        DB::table('Deliverable')->where('deliverable_id', $ddi)->delete();
                    }
                }
				
            session()->flash('cohort',  $cohort);
            session()->flash('faculty', $faculty);
            session()->flash('isCompetition',  INPUT::get('competition'));
            return redirect("/managedeliverable")->with("success", "Deliverable updated!")->withInput($request->input());
        }
        //cohort_deliverable_id does not exist
        else {
            if (empty($deliverable_id)) {
                session()->flash('cohort',  $cohort);
                session()->flash('faculty', $faculty);
                session()->flash('isCompetition',  INPUT::get('competition'));
                return redirect("/managedeliverable")->with("error", "No entry was entered!")->withInput($request->input());
            } else {
                //Create cohort deliverable
                $cohort_deliverable = new Cohort_deliverable();
                $cohort_deliverable->facultyID = $facultyID;
                //$cohort_deliverable->programmeID = $programmeID;
                $cohort_deliverable->cohortID = $cohort;
                $cohort_deliverable->isCompetition = $isCompetition;
                $cohort_deliverable->save();
                $cohort_deliverable_id = $cohort_deliverable->id;

                //Insert deliverable
                for ($i = 0; $i < sizeOf($deliverable_id_array); $i++) {
                    $deliverable = new Deliverable();
                    $deliverable->deliverable_name = $deliverable_name[$i];
                    if ($showcase[$i] == "True")
                        $deliverable->showcase = 1;
                    else
                        $deliverable->showcase = 0;
                    $deliverable->cohort_deliverable_id = $cohort_deliverable_id;
                    $deliverable->deliverable_type_id = $deliverable_type_id_array[$i];
                    $deliverable->save();
                }
                session()->flash('cohort',  $cohort);
                session()->flash('faculty', $faculty);
                session()->flash('isCompetition',  INPUT::get('competition'));
                return redirect("/managedeliverable")->with("success", "Table created and deliverable updated!")->withInput($request->input());
            }
        }
        return;

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
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
