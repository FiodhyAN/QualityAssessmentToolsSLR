<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleUserQuestionaire;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Questionaire;
use App\Models\User;
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

        // variable for chart
        $question_name = Questionaire::select('name')->pluck('name')->toArray();
        $user_name = User::select('name')->whereHas('project_user', function($query){
            $query->where('project_id', request()->pid)->where('user_role', 'reviewer');
        })->pluck('name')->toArray();

        $pos_answer = ArticleUserQuestionaire::with('questionaire')->whereHas('articleUser', function($query){
            $query->whereHas('article', function($query){
                $query->where('project_id', request()->pid);
            });
        })->where('score', 1)->get();

        return $pos_answer;

        return view('dashboard.admin.summary.summary', compact('articles', 'question_name', 'user_name'));
    }
}
