<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Project;
use App\Models\ProjectUser;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function projectIndex()
    {
        $projects = ProjectUser::with('project')->where('user_id', auth()->user()->id)->where('user_role', 'admin')->get();
        return view('dashboard.admin.summary.project', compact('projects'));
    }

    public function projectSummary()
    {
        $articles = Article::with(['project', 'article_user' => function($query){
            $query->with('user');
        }])->where('project_id', request()->pid)->whereHas('article_user', function($query){
            $query->where('is_assessed', false);
        })->get();
        return view('dashboard.admin.summary.summary', compact('articles'));
    }
}
