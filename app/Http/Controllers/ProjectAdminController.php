<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectUser;
use App\Models\Project;
use App\Models\Article;
use App\Models\User;
use Yajra\DataTables\DataTables;

class ProjectAdminController extends Controller
{
    public function index()
    {
        $this->authorize('admin');
        $projects = ProjectUser::with('project')->where('user_id', auth()->user()->id)->where('user_role', 'admin')->get();
        return view('dashboard.admin.project', compact('projects'));
    }

    public function show($id)
    {
        $this->authorize('admin');
        
        $project_limit = Project::where('id', $id)->first();
        $user_project = ProjectUser::where('project_id', $id)->where('user_role', 'reviewer')->count();

        if ($user_project == $project_limit->limit_reviewer) {
            $users = User::with(['article_user' => function($query) use ($id) {
                $query->with('article')->whereHas('article', function($query) use ($id) {
                    $query->where('project_id', $id);
                });
            }])->where('id', '!=', auth()->user()->id)->where('is_superadmin', false)->whereHas('article_user', function($query) use ($id) {
                $query->whereHas('article', function($query) use ($id) {
                    $query->where('project_id', $id);
                });
            })->get();
        }
        else {
            $users = User::with(['article_user' => function($query) use ($id) {
                $query->with('article')->whereHas('article', function($query) use ($id) {
                    $query->where('project_id', $id);
                });
            }])->where('id', '!=', auth()->user()->id)->where('is_superadmin', false)->get();
        }
        // return $users;
        $project = ProjectUser::with('project')->where('user_id', auth()->user()->id)->where('project_id', $id)->first();
        return view('dashboard.admin.article.index', [
            'project' => $project,
            'users' => $users,
        ]);
    }

    
}
