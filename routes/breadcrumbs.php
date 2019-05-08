<?php

use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

//Breadcrumbs::for('projecthome', function ($trail) {
//   $trail->push('Project', route('project_home'));
//});

Breadcrumbs::for('projectrepository', function($trail, $project){
    //$trail->parent('projecthome');
    $trail->push($project->title, route('project_repository', $project->projectCode));
});

Breadcrumbs::for('createrepository',function($trail, $project){
    $trail->parent('projectrepository', $project);
    $trail->push('New Repository', route('form_create_repository', $project->projectCode));
});

Breadcrumbs::for('viewrepository', function ($trail, $repo, $project) {
    $trail->parent('projectrepository', $project);
    $trail->push($repo->name, route('project_display_repository', ['id'=>$repo->id, 'br'=>'master']));
});






Breadcrumbs::for('repoaddstudents',function($trail,$project, $repoid){
    $trail->parent('projectrepository',$project);
    try {
        $trail->push($repoid->name, route('form_add_students_to_repo', ['id'=>$project->id,'rid'=>$repoid->id]));
    } catch (ErrorException $e) {
        return redirect()->route('project_home')->with('error', 'Please do not jump pages');
    }
});

Breadcrumbs::for('studenthome', function ($trail) {
    $trail->push('Repositories', route('student_home'));
});

Breadcrumbs::for('repository', function ($trail, $repo) {
    $trail->parent('studenthome');
    $trail->push($repo->name, route('student_display_repository', ['id'=>$repo->id, 'br'=>'master']));
});