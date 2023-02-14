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
        return view('dashboard.superAdmin.project', [
            'projects' => Project::with('user')->get(),
            'users' => User::where('id', '!=', auth()->user()->id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('superadmin');
        $request->validate([
            'project_name' => 'required',
            'limit' => 'required',
            'admin_project' => 'required',
        ]);
        Project::create([
            'project_name' => $request->project_name,
            'limit_reviewer' => $request->limit,
            'user_id' => $request->admin_project,
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

        return redirect()->back()->with('success', 'Project Updated Successfully');
    }
}
