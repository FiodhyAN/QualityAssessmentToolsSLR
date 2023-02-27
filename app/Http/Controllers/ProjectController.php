<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $this->authorize('superadmin');
        $project = Project::with(['project_user' => function($query){
            $query->with('user')->where('user_role', 'admin');
        }])->get();
        return view('dashboard.superAdmin.project', [
            'projects' => $project,
            'users' => User::where('id', '!=', auth()->user()->id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('superadmin');
        $request->validate([
            'project_name' => 'required|unique:projects,project_name',
            'limit' => 'required',
            'admin_project' => 'required',
        ]);
        Project::create([
            'project_name' => $request->project_name,
            'limit_reviewer' => $request->limit,
        ]);
        ProjectUser::create([
            'project_id' => Project::latest()->first()->id,
            'user_id' => $request->admin_project,
            'user_role' => 'admin',
        ]);
        User::where('id', $request->admin_project)->update([
            'is_admin' => true,
        ]);

        return redirect()->back()->with('success', 'Project Created Successfully');
    }

    public function update(Request $request)
    {
        $this->authorize('superadmin');
        $request->validate([
            'project_name' => 'required',
            'limit' => 'required',
        ]);
        Project::where('id', $request->project_id)->update([
            'project_name' => $request->project_name,
            'limit_reviewer' => $request->limit,
        ]);
        if ($request->old_admin != $request->admin_project) {
            ProjectUser::where('project_id', $request->project_id)->where('user_id', $request->old_admin)->update([
                'user_role' => 'reviewer',
            ]);
            ProjectUser::where('project_id', $request->project_id)->where('user_id', $request->admin_project)->update([
                'user_role' => 'admin',
            ]);
            User::where('id', $request->admin_project)->update([
                'is_admin' => true,
            ]);
        }

        $user = User::with(['project_user' => function($query) use ($request){
            $query->where('user_role', 'admin');
        }])->where('id', $request->old_admin)->first();

        if ($user->project_user->count() == 0) {
            User::where('id', $request->old_admin)->update([
                'is_admin' => false,
            ]);
        }
        else {
            User::where('id', $request->old_admin)->update([
                'is_admin' => true,
            ]);
        }
        return redirect()->back()->with('success', 'Project Updated Successfully');
    }

    public function delete(Request $request)
    {
        $this->authorize('superadmin');
        
        Project::where('id', $request->id)->delete();
        ProjectUser::where('project_id', $request->id)->delete();

        $users = User::with('project_user')->get();
        
        foreach ($users as $user) {
            if ($user->project_user->count() == 0) {
                $user->update([
                    'is_admin' => false,
                ]);
            }
        }
    }

    public function findProjectUser(Request $request)
    {
        $this->authorize('superadmin');
        $project = Project::with(['project_user' => function($query){
            $query->with('user')->orderBy('user_role');
        }])->where('id', $request->id)->first();
        return $project->project_user;
    }
}
