<?php

namespace App\Http\Controllers;

use App\Deliverable_type;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ManageDeliverableTypeController extends Controller
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
        //if index() is called by redirect, set active tab to the tab before redirect
        if(session('is_redirect') == 'true'){
            session()->flash('active_tab', session('active_tab'));
        }
        //else set active tab to first tab
        else{
            session()->flash('active_tab', '0');
        }
        session()->flash('is_redirect', 'false');
		
         $staff = DB::table('staff')->select('role')->where('staffId', Auth::user()->staffId)->get();
        //check if page is being accessed by FYPcommittee
        if($staff[0]->role == "admin" || $staff[0]->role == "fypcommittee")
            return view('pages.manage_deliverable.manage_deliverable_type');
        else
			return view("index");
            
    }

    public function ajax_load_deliverable_type()
    {
        $deliverable_types = DB::table('Deliverable_Type')->select('deliverable_type_id', 'deliverable_type')->orderBy('deliverable_type','asc')->get();
        return json_encode($deliverable_types);
    }

    public function ajax_change_deliverable_type()
    {
        $deliverable_type_id = INPUT::get('id');
        $deliverable_types = DB::table('Deliverable_Type')->select('deliverable_type', 'deliverable_extension', 'field_type')->where('deliverable_type_id', $deliverable_type_id)->get();
        return json_encode($deliverable_types);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illu
minate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'deliverable_name' => 'required',
            'deliverable_extension' => 'required'
        ]);

        $action = INPUT::get('action');
        $deliverable_type_id = INPUT::get('deliverable_type_id');
        if ($action == 'delete') {
            //if deliverable_count > 1, the deliverable type cannot be deleted since there is other deliverable usigng it
            $deliverable_count = DB::table('Deliverable')->where('deliverable_type_id', $deliverable_type_id)->count();
            if($deliverable_count < 1) {
                DB::table('Deliverable_Type')->where('deliverable_type_id', $deliverable_type_id)->delete();
                session()->flash('active_tab', '1');
                session()->flash('is_redirect', 'true');
                return redirect("/managedeliverabletype")->with("success", "Deliverable Type deleted!");
            }
            else{
                session()->flash('active_tab', '1');
                session()->flash('is_redirect', 'true');
                session()->flash('selected_id', $deliverable_type_id);
                return redirect("/managedeliverabletype")->with("error", "This deliverable cannot be deleted.");
            }
        }
        else if ($action == 'update') {
            $deliverable_type = Deliverable_type::find($deliverable_type_id);
            $deliverable_type->deliverable_type = INPUT::get('deliverable_name');
            $deliverable_type->deliverable_extension = INPUT::get('deliverable_extension');
            $deliverable_type->field_type = INPUT::get('deliverable_field_type');
            $deliverable_type->save();
            session()->flash('active_tab', '1');
            session()->flash('is_redirect', 'true');
            session()->flash('selected_id', $deliverable_type_id);
            return redirect("/managedeliverabletype")->with("success", "Deliverable type updated!");

        }
        else if ($action == 'insert') {
            $deliverable_type = new Deliverable_type();
            $deliverable_type->deliverable_type = INPUT::get('deliverable_name');
            $deliverable_type->deliverable_extension = INPUT::get('deliverable_extension');
            $deliverable_type->field_type = INPUT::get('deliverable_field_type');
            $deliverable_type->save();
            session()->flash('active_tab', '0');
            session()->flash('is_redirect', 'true');
            return redirect("/managedeliverabletype")->with("success", "New deliverable type added!");
        }

        return redirect("/managedeliverabletype")->with("error", "No entry was entered!");
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
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return "show";
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
        return "edit";
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
        return "update";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return "destroy";
    }
}
