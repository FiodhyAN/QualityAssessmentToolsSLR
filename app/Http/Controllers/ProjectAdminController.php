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
        $projects = ProjectUser::with('project')->where('user_id', auth()->user()->id)->get();
        return view('dashboard.admin.project', compact('projects'));
    }

    public function show($id)
    {
        $this->authorize('admin');
        $users = User::with(['article_user' => function($query) {
            $query->with('article');
        }])->where('id', '!=', auth()->user()->id)->where('is_superadmin', false)->get();
        $project = ProjectUser::with('project')->where('user_id', auth()->user()->id)->where('project_id', $id)->first();
        return view('dashboard.admin.article.index', [
            'project' => $project,
            'users' => $users,
        ]);
    }

    
}
