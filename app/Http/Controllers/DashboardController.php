<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleUser;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->is_superAdmin) {
            $total_user = count(User::where('is_superAdmin', false)->get());
            $total_project = count(Project::all());
            $total_article_assessed = count(ArticleUser::where('is_assessed', true)->get());
            $total_article_not_assessed = count(ArticleUser::where('is_assessed', false)->get());
            $project_name = Project::pluck('project_name');
            $total_article = count(Article::all());

            $data_project_assessed = Project::with(['article' => function($query){
                $query->with('article_user')->whereHas('article_user', function($query){
                    $query->where('is_assessed', true);
                });
            }])->get();
            return $data_project_assessed;
            $project_assessed = [];
            foreach ($data_project_assessed as $key => $value) {
                $project_assessed[$key] = $value->article->count();
            }

            $data_project_not_assessed = Project::with(['article' => function($query){
                $query->whereHas('article_user', function($query){
                    $query->where('is_assessed', false);
                });
            }])->get();
            $project_not_assessed = [];
            foreach ($data_project_not_assessed as $key => $value) {
                $project_not_assessed[$key] = $value->article->count();
            }

            return view('dashboard.superAdmin.index', [
                'total_user' => $total_user,
                'total_project' => $total_project,
                'article_assessed' => $total_article_assessed,
                'article_not_assessed' => $total_article_not_assessed,
                'project_name' => $project_name,
                'project_assessed' => $project_assessed,
                'project_not_assessed' => $project_not_assessed,
                'total_article' => $total_article,
                'article_not_assign' => $total_article - $total_article_assessed - $total_article_not_assessed
            ]);
        }
        elseif (auth()->user()->is_admin) {
            return view('dashboard.admin.index');
        }
        else {
            return view('dashboard.reviewer.index');
        }
    }
}
