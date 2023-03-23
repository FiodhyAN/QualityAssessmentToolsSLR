<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleUser;
use App\Models\ArticleUserQuestionaire;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    public function index()
    {
        $this->authorize('superadmin');
        $project = Project::with(['project_user' => function($query){
            $query->with('user')->where('user_role', 'admin');
        }])->get();
        // return $project[0]->project_user[0]->user->name;
        return view('dashboard.superAdmin.project', [
            'projects' => $project,
            'users' => User::where('id', '!=', auth()->user()->id)->get(),
        ]);
    }

    public function projectTable()
    {
        $this->authorize('superadmin');
        $project = Project::with(['project_user' => function($query){
            $query->with('user')->where('user_role', 'admin');
        }])->orderBy('id')->get();
        return DataTables::of($project)
            ->addIndexColumn()
            ->addColumn('project_name', function(Project $project){
                return $project->project_name;
            })
            ->addColumn('limit_reviewer', function(Project $project){
                return $project->limit_reviewer;
            })
            ->addColumn('admin_project', function(Project $project){
                $name = $project->project_user[0]->user->name;
                return $name;
            })
            ->addColumn('action', function(Project $row){
                $btn = '<button type="button" class="btn btn-primary btn-sm aksi" data-toggle="modal" data-bs-target="#modalEdit" data-id="'.$row->id.'" data-project_name="'.$row->project_name.'" data-limit="'.$row->limit_reviewer.'" data-admin_project="'.$row->project_user[0]->user->id.'"><ion-icon name="create-outline"></ion-icon> Edit</button>';
                $btn .= '<button type="button" class="btn btn-danger btn-sm ms-2 aksi deleteProject" data-id="'.$row->id.'"><ion-icon name="trash-outline"></ion-icon> Delete</button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $this->authorize('superadmin');
        $request->validate([
            'project_name' => 'required|unique:projects,project_name',
            'limit' => 'required',
            'admin_project' => 'required',
        ]);
        $project = Project::create([
            'project_name' => $request->project_name,
            'limit_reviewer' => $request->limit,
        ]);
        ProjectUser::create([
            'project_id' => $project->id,
            'user_id' => $request->admin_project,
            'user_role' => 'admin',
        ]);
        foreach ($request->reviewer as $reviewer) {
            ProjectUser::create([
                'project_id' => $project->id,
                'user_id' => $reviewer,
                'user_role' => 'reviewer',
            ]);
        }
        User::where('id', $request->admin_project)->update([
            'is_admin' => true,
        ]);

        // return ->with('success', 'Project Created Successfully');
        // return json_encode(['success' => 'Project Created Successfully']);
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
        $articles = Article::where('project_id', $request->id)->get();
        foreach ($articles as $article) {
            $articleUser = ArticleUser::where('article_id', $article->id);
            foreach ($articleUser as $au) {
                ArticleUserQuestionaire::where('article_user_id', $au->id)->delete();
            }
            $articleUser->delete();
        }
        Article::where('project_id', $request->id)->delete();

        
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
        return $project->project_user->toJson();
    }

    public function findReviewer(Request $request)
    {
        $this->authorize('superadmin');
        $user = User::where('id', '!=', $request->user_id)->where('is_superadmin', '!=', true)->where('id', '!=', auth()->user()->id)->get();
        return $user->toJson();
    }

    public function findEditReviewer(Request $request)
    {
        $this->authorize('superadmin');
        $user = User::where('id', '!=', $request->user_id)->where('is_superadmin', '!=', true)->where('id', '!=', auth()->user()->id)->with('project_user', function($query) use ($request){
            $query->where('project_id', $request->project_id);
        })->get();
        return $user->toJson();
    }
}
