<?php
use App\Staff;
/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */
Route::get('/', function () {
    return view('welcome');
});

Route::get('/index',function()
{
    $fypstaff = Staff::all();
    return view('index', compact('fypstaff'));
});

Route::get('/Staffindex',function()
{
    return view('Staffindex');
})->middleware('auth:staff');


// Tan Yi Ying
Route::get('rubric', function () {
    return view('rubric.rubricMain');
});

Route::get('formMain', function () {
    return view('form.formMain');
});

Route::post('formIndex', 'FormController@formIndex');
Route::post('rubricIndex', 'RubricController@rubricIndex');

//Form 1
Route::post('storeForm1', 'FormController@storeForm1');

//Form 2
Route::post('downloadProposalTemplate', 'FormController@downloadProposalTemplate');
Route::post('storeProposal', 'FormController@storeProposal');
Route::post('deleteProposal', 'FormController@deleteProposal');
Route::post('downloadSuperviseProposal', 'FormController@downloadSuperviseProposal');
Route::post('downloadModerateProposal', 'FormController@downloadModerateProposal');

//Form 3
Route::post('createForm3', 'FormController@createForm3');
Route::post('storeForm3/{role}', 'FormController@storeForm3');
Route::get('printForm3', 'FormController@printForm3PDF');

//Form 4
Route::post('downloadForm4iTemplate', 'FormController@downloadForm4iTemplate');
Route::post('downloadForm4iiTemplate', 'FormController@downloadForm4iiTemplate');

//Form Template
Route::post('storeFormTemplate', 'FormController@storeFormTemplate');
Route::post('deleteFormTemplate', 'FormController@deleteFormTemplate');

//Template Version
Route::post('storeTemplateVersion', 'RubricController@storeTemplateVersion');

//Student Assessment
Route::post('studentRubric', 'RubricController@studentRubric');
Route::post('studentMark/{role}', 'RubricController@studentMark');
Route::post('printStudentRubric', 'RubricController@printStudentRubric');
Route::post('getStudents', 'RubricController@getStudents');

//Print Mark Summary
Route::post('printMarkSummary', 'RubricController@getMarkSummaryProject');

//Rubric Template
Route::post('uploadRubric', 'RubricController@uploadRubric');
Route::post('deleteRubricTemplate', 'RubricController@deleteRubricTemplate');
Route::post('downloadProject1Rubric', 'RubricController@downloadProject1Rubric');
Route::post('downloadProject2Rubric', 'RubricController@downloadProject2Rubric');

//Template Status
Route::get('uploadFormTemplate', function() {
    return view('form.uploadFormTemplate');
});

Route::get('removeFormTemplate', function() {
    return view('form.uploadFormTemplate');
});

Route::get('removeRubricTemplate', function() {
    return view('rubric.uploadRubric');
});

Route::get('templateVersion', function() {
    return view('rubric.rubricMain');
});

//Form Status
Route::get('uploadForm2', function() {
    return view('form.form2');
});

Route::get('unsubmitForm2', function() {
    return view('form.form2');
});

Route::get('invalidProposalUpload', function() {
    return view('form.form2');
});

Route::get('invalidRubricUpload', function() {
    return view('rubric.uploadRubric');
});

Route::get('invalidFormUpload', function() {
    return view('form.uploadFormTemplate');
});

Route::get('errorForm2', function() {
    return view('form.form2');
});

Route::get('errorForm4i', function() {
    return view('form.form4');
});

Route::get('errorForm4ii', function() {
    return view('form.form4');
});

Route::get('errorProject1Rubric', function() {
    return view('rubric.downloadRubric');
});

Route::get('errorProject2Rubric', function() {
    return view('rubric.downloadRubric');
});


//Yap Kai Jean
Route::resource('projectlist', 'ProjectListController');
Route::resource('project', 'ProjectController');
Route::resource('workload', 'WorkloadController');

Route::get('/viewproject', [
    'uses' => 'ProjectController@viewproject',
    'as' => 'project.viewproject'
]);

Route::post('/viewproject', [
    'uses' => 'ProjectController@viewproject',
    'as' => 'project.viewproject'
]);

Route::get('/viewprojectlist', [
    'uses' => 'ProjectListController@viewprojectlist',
    'as' => 'projectlist.viewprojectlists'
]);

Route::post('/viewprojectlist', [
    'uses' => 'ProjectListController@viewprojectlist',
    'as' => 'projectlist.viewprojectlists'
]);

Route::get('/viewworkload', [
    'uses' => 'WorkloadController@viewworkload',
    'as' => 'workload.viewworkload'
]);

Route::post('/viewworkload', [
    'uses' => 'WorkloadController@viewworkload',
    'as' => 'workload.viewworkload'
]);

Route::post('/createproject', [
    'uses' => 'ProjectController@create',
    'as' => 'project.createproject'
]);

Route::get('/createproject', [
    'uses' => 'ProjectController@create',
    'as' => 'project.createproject'
]);

Route::post('/updateproject/{id}', [
    'uses' => 'ProjectController@updateproject',
    'as' => 'project.updateproject'
]);

Route::get('/updateproject/{id}', [
    'uses' => 'ProjectController@updateproject',
    'as' => 'project.updateproject'
]);

Route::get('/removeproject/{id}', [
    'uses' => 'ProjectController@removeproject',
    'as' => 'project.removeproject'
]);

Route::post('/viewprojectdetails/{id}', [
    'uses' => 'ProjectListController@view',
    'as' => 'projectlist.compareprojectlist'
]);

Route::post('/updateprojectlist/{id}', [
    'uses' => 'ProjectListController@edit',
    'as' => 'projectlist.updateprojectlist'
]);

Route::post('staffName/{staffName}/cohortID/{cohortID}/section/{selectedSec}', [
    'as' => 'workload.viewworkloaddetails', 'uses' => 'WorkloadController@view']);

Route::get('/updateformula', [
    'uses' => 'WorkloadController@edit',
    'as' => 'workload.updateformula'
]);

Route::post('/updateformula', [
    'uses' => 'WorkloadController@edit',
    'as' => 'workload.updateformula'
]);

Route::post('/generateworkloadreport', [
    'uses' => 'WorkloadController@generate',
    'as' => 'workload.generateworkloadreport'
]);


//Tee Sheng Kang
//cohort
Route::get('/maintaincohort','CohortController@index');
Route::get('/crcohort', function(){
    return view('CRcohort');
})->middleware('auth:staff')->name('crcohort');
Route::get('/staffpage', function(){
    return view('Staffpage');
})->middleware('auth:staff')->name('staffpage');
Route::get('/cohortmenu', function(){
    return view('CohortMenu');
})->middleware('auth:staff');
Route::get('/cohortmenu/{id}','CohortController@showmenu');
Route::post('createcohort','CohortController@store')->name('createcohort');
Route::get('showallcohort','CohortController@showall')->name('showallcohort');
Route::post('/deletecohort','CohortController@destroy')->name('deletecohort');
Route::get('editcohort/{id}','CohortController@edit');
Route::post('updatecohort/{id}','CohortController@update')->name('updatecohort');
Route::get('orderby/{order}','CohortController@orderby');


//supervisor_cohort
Route::get('/staffpairing/{id}','SupcohortController@show')->name('staffpairing');
Route::post('/storepairing','SupcohortController@arrstore');
Route::post('/Addsupervisor/storesupervisor','SupcohortController@addsupervisor');
Route::get('/Addsupervisor/{id}/{staffId}','SupcohortController@addsupervisorpage')->name('addsupervisor');

//staff
Route::post('/createcohort/search','StaffController@searchbyname');
Route::post('/addadminpage/search_name_faculty','StaffController@searchbynamefaculty');
Route::post('/addadminpage/updatestaffrole','StaffController@updatestaffrole_fadmin');
Route::post('/addfyppage/updatestaffrole','StaffController@updatestaffrole_fyp');
Route::get('/addfadminpage','StaffController@addfadminpage');
Route::get('/addfyppage','StaffController@addfyppage');
Route::get('/importstaff', function(){
    return view('ImportStaff');
})->middleware('auth:staff');
Route::post('importstaff','StaffController@import')->name('importstaff');
Route::get('/addnewstaffpage','StaffController@addnewstaffpage');
Route::post('/addnewstaff','StaffController@addnewstaff')->name('addnewstaff');
Route::get('/deactivatestaff','StaffController@deactivatestaffpage');
Route::post('/deletestaff','StaffController@destroy');
Route::post('/activatestaff','StaffController@activatestaff');
Route::get('/staffprofile','StaffController@staffprofilepage');
Route::post('/staffupdateprofile','StaffController@updateprofile');

//student
Route::post('importstudent','StudentController@import')->name('importstudent');
Route::get('studentmaintenance', function(){
    return view('StudentMaintenance');
})->middleware('auth:staff');
Route::get('/studentprofile','StudentController@studentprofilepage');
Route::post('/studentupdateprofile','StudentController@updateprofile');

//project
Route::post('importproject','ProjectController@import')->name('importproject');

//team
Route::post('importteam','TeamController@import')->name('importteam');

//erro
Route::get('/errorpage', function(){
    return view('errorpage');
});

//google
Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('auth/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


//Tee Ren Mian
Route::get('RegisterTeam', 'studentsController@showRegisterTeam')->name('RegisterTeam');
Route::get('getLatestTeamId','teamsController@getLatestTeamId');
Route::get('addTeam/{studentId}/{team}','studentsController@addTeam');
Route::get('cancelTeam/{studentId}','studentsController@cancelTeam');
Route::post('/TeamTable/{id}','studentsController@viewStudent');
Route::post('/registerTeamTable/{teamId}','studentsController@addTeam');
Route::get('/projectShowTeam','ProjectController@showAllTeam')->name('RegisterProject');
Route::get('/showAllTeam','teamsController@showAllTeam')->name('showAllTeam');
Route::post('/registerProject','project_supervisorController@showProject');
Route::post('/registerProjectTable','project_supervisorController@RegNewProj');
Route::post('/studentSupervisorTable/{cohortId}','supervisor_cohortController@viewAllSupervisor');
Route::post('/studentSupervisorTable1/{cohortId}','studentsController@viewAllStudent');
Route::get('/studentSupervisor','CohortController@studSpvList')->name('studentSupervisorList');
Route::get('/ProjectConfirmation','teamsController@ApproveProject')->name('ApproveProjectRegistration');
Route::post('/PendingProject/{supervisor}','teamsController@showAllProject');
Route::post('/AcceptProject','teamsController@respondProject');
Route::post('/RejectProject','teamsController@respondProject');
Route::post('/showTable','teamsController@allTeam');
Route::get('/autoAssign','teamsController@autoAssign');

Route::get('/MianHome','teamsController@LinkHome')->name('Home');

//leongcy
Route::get('selectprojectcompetition', 'SelectProjectCompetitionContoller@index')->name('selectprojectcompetition');
Route::get('selectprojectcompetition/ajax_load_previously_selected','SelectProjectCompetitionContoller@ajax_load_previously_selected');
Route::get('selectprojectcompetition/ajax_load_unselected','SelectProjectCompetitionContoller@ajax_load_unselected');
Route::post('selectprojectcompetition/select','SelectProjectCompetitionContoller@select');
Route::post('selectprojectcompetition/unselect','SelectProjectCompetitionContoller@unselect');
Route::get('projectsubmission/{isCompetition?}', 'ProjectSubmissionController@index')->name('projectsubmission');
Route::resource('projectsubmission', 'ProjectSubmissionController');
Route::get('managedeliverable/ajax_load_deliverable', 'ManageDeliverableController@ajax_load_deliverable');
Route::get('managedeliverable/ajax_load_programme', 'ManageDeliverableController@ajax_load_programme');
Route::get('managedeliverable/ajax_add_deliverable', 'ManageDeliverableController@ajax_add_deliverable');
Route::get('managedeliverable/ajax_change_deliverable_extension', 'ManageDeliverableController@ajax_change_deliverable_extension');
Route::get('managedeliverable/ajax_copy_yes', 'ManageDeliverableController@ajax_copy_yes');
Route::get('managedeliverable/index', 'ManageDeliverableController@index')->name('managedeliverable');
Route::resource('managedeliverable', 'ManageDeliverableController');
Route::get('managedeliverabletype/ajax_load_deliverable_type', 'ManageDeliverableTypeController@ajax_load_deliverable_type');
Route::get('managedeliverabletype/ajax_change_deliverable_type', 'ManageDeliverableTypeController@ajax_change_deliverable_type');
Route::get('managedeliverabletype/index', 'ManageDeliverableTypeController@index')->name('managedeliverabletype');
Route::resource('managedeliverabletype', 'ManageDeliverableTypeController');
Route::get('/displaysubmission', 'DisplaySubmissionController@index')->name('displaysubmission');
Route::get('/displaysubmission/show/{projectCode}/{teamID}/{isCompetition}', 'DisplaySubmissionController@show')->name('displaysubmission.show');
Route::get('/displaysubmission/download/{item_id}', 'DisplaySubmissionController@download')->name('displaysubmission.download');
Route::get('/displaysubmission/remove/{submission_id}', 'DisplaySubmissionController@remove')->name('displaysubmission.remove');
//Route::get('placeholder', 'PlaceholderController@index')->name('placeholder');

// lau 
//Route::get('/project', 'ProjectController2@index')->name('project_home');// project home, show project list under currently logged in staff
Route::get('/project/{id}/repository', 'ProjectController2@repository')->name('project_repository');// show project_repository

Route::get('/project/{id}/createrepository','ProjectController2@formcreaterepository')->name('form_create_repository');// form to create repository
Route::post('/project/createrepository/redirect','ProjectController2@createrepository')->name('post_create_repo');// post page to create repository

//Route::get('/project/assignteam', 'ProjectController2@formassignteam')->name('form_assign_team');// form assign team to project
//Route::post('/project/assignteam/redirect', 'ProjectController2@assignteam')->name('assign_team');// post page to assign team to project

Route::get('/project/{id}/repository/{rid}/', 'ProjectController2@formaddstudenttorepo')->name('form_add_students_to_repo');// form add students to repository
Route::post('/project/repository/addstudents', 'ProjectController2@addstudenttorepo')->name('add_students_to_repo');// post page to add students to repository

Route::get('/student', 'StudentController2@index')->name('student_home');
Route::get('/student/repository/{id}/{br}', 'StudentController2@displayrepository')->name('student_display_repository');

// staff
Route::get('/project/repository/{id}/{br}', 'ProjectController2@displayrepository')->name('project_display_repository');

Route::get('/viewfile/{id}/{branch}/{hash}', 'RepositoryFolder@viewFile')->name('viewfile');

Route::post('/project/repository/addtags', 'ProjectController2@addRepoTags')->name('addtags');
Route::get('/generatekeys', 'ProjectController2@addKeys')->name('addkeys');