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
        $this->authorize('projectSummary');
        if (auth()->user()->is_superAdmin) {
            $projects = Project::all();
        }
        else {
            $projects = ProjectUser::with('project')->where('user_id', auth()->user()->id)->where('user_role', 'admin')->get();
        }
        return view('dashboard.summary.project', compact('projects'));
    }

    public function projectSummary()
    {
        $this->authorize('projectSummary');
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


        $pos_answer_question = [];
        $net_answer_question = [];
        $neg_answer_question = [];
        $pos_answer_user = [];
        $net_answer_user = [];
        $neg_answer_user = [];

        // positive answer for question chart
        $data_pos_answer_question = Questionaire::with(['article_user_questionaire' => function($query){
            $query->whereHas('articleUser', function($query){
                $query->whereHas('article', function($query){
                    $query->where('project_id', request()->pid);
                });
            })->where('score', 1);
        }])->get();
        foreach ($data_pos_answer_question as $key => $value) {
            $pos_answer_question[$key] = $value->article_user_questionaire->count();
        }

        // netral answer for question chart
        $data_net_answer_question = Questionaire::with(['article_user_questionaire' => function($query){
            $query->whereHas('articleUser', function($query){
                $query->whereHas('article', function($query){
                    $query->where('project_id', request()->pid);
                });
            })->where('score', 0);
        }])->get();
        foreach ($data_net_answer_question as $key => $value) {
            $net_answer_question[$key] = $value->article_user_questionaire->count();
        }

        // negative answer for question chart
        $data_neg_answer_question = Questionaire::with(['article_user_questionaire' => function($query){
            $query->whereHas('articleUser', function($query){
                $query->whereHas('article', function($query){
                    $query->where('project_id', request()->pid);
                });
            })->where('score', -1);
        }])->get();
        foreach ($data_neg_answer_question as $key => $value) {
            $neg_answer_question[$key] = $value->article_user_questionaire->count();
        }

        $data_pos_answer_user = User::whereHas('project_user', function($query){
            $query->where('project_id', request()->pid)->where('user_role', 'reviewer');
        })->with(['article_user' => function($query){
            $query->whereHas('questionaires', function($query){
                $query->where('score', 1);
            })->whereHas('article', function($query){
                $query->where('project_id', request()->pid);
            });
        }])->get();
        foreach ($data_pos_answer_user as $key => $value) {
            $pos_answer_user[$key] = $value->article_user->count();
        }

        $data_net_answer_user = User::whereHas('project_user', function($query){
            $query->where('project_id', request()->pid)->where('user_role', 'reviewer');
        })->with(['article_user' => function($query){
            $query->whereHas('questionaires', function($query){
                $query->where('score', 0);
            })->whereHas('article', function($query){
                $query->where('project_id', request()->pid);
            });
        }])->get();
        foreach ($data_net_answer_user as $key => $value) {
            $net_answer_user[$key] = $value->article_user->count();
        }

        $data_neg_answer_user = User::whereHas('project_user', function($query){
            $query->where('project_id', request()->pid)->where('user_role', 'reviewer');
        })->with(['article_user' => function($query){
            $query->whereHas('questionaires', function($query){
                $query->where('score', -1);
            })->whereHas('article', function($query){
                $query->where('project_id', request()->pid);
            });
        }])->get();
        foreach ($data_neg_answer_user as $key => $value) {
            $neg_answer_user[$key] = $value->article_user->count();
        }

        return view('dashboard.summary.summary', compact('articles', 
                                                                'question_name', 
                                                                'user_name', 
                                                                'pos_answer_question', 
                                                                'net_answer_question',
                                                                'neg_answer_question',
                                                                'pos_answer_user',
                                                                'net_answer_user',
                                                                'neg_answer_user'
                                                                ));
    }
}
