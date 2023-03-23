<?php

namespace App\Http\Controllers;

use App\Models\ProjectUser;

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
        $project = ProjectUser::with('project')->where('user_id', auth()->user()->id)->where('project_id', $id)->first();
        return view('dashboard.admin.article.index', [
            'project' => $project,
        ]);
    }

    
}
