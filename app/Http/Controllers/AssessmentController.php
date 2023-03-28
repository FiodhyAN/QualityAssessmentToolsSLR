<?php

namespace App\Http\Controllers;

use App\Models\ArticleUser;
use App\Models\ArticleUserQuestionaire;
use App\Models\Project;
use App\Models\Questionaire;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AssessmentController extends Controller
{
    public function index()
    {
        $this->authorize('reviewer');
        $questionaires = Questionaire::all();
        $projects = Project::select('id','project_name')->whereHas('project_user', function($query) {
            $query->where('user_id', auth()->user()->id);
        })->get();
        return view('dashboard.reviewer.assessment', compact('questionaires', 'projects'));
    }
    
    public function assessmentTable(Request $request)
    {
        $this->authorize('reviewer');
        // return dd($request->all());
        $articles = [];
        if ($request->has('project_id') && $request->project_id != 'all') {
            $data = ArticleUser::with(['article' => function($query) use ($request) {
                $query->with('project')->whereHas('project', function($query) use ($request) {
                    $query->where('id', $request->project_id);
                });
            }])->where('user_id', auth()->user()->id)->where('is_assessed', false)->get();
    
            foreach ($data as $key => $value) {
                if ($value->article != null) {
                    $articles[] = $value;
                }
            }
        }
        else {
            $articles = ArticleUser::with(['article' => function($query) {
                $query->with('project');
            }])->where('user_id', auth()->user()->id)->where('is_assessed', false)->get()->sortBy('article.project.project_name');
        }

        return DataTables::of($articles)
            ->addColumn('no', function(ArticleUser $article){
                return $article->article->id.' - '.$article->article->no;
            })
            ->addColumn('title', function(ArticleUser $article){
                return $article->article->title;
            })
            ->addColumn('project_name', function(ArticleUser $article) {
                return $article->article->project->project_name;
            })
            ->addColumn('year', function(ArticleUser $article) {
                return $article->article->year;
            })
            ->addColumn('publication', function(ArticleUser $article){
                return $article->article->publication;
            })
            ->addColumn('authors', function(ArticleUser $article){
                return $article->article->authors;
            })
            ->addColumn('action', function(ArticleUser $article){
                return '<button class="btn btn-primary btn-sm" id="btn_assessment" data-bs-toggle="modal" data-bs-target="#exampleModal" data-article_id="'.$article->article->id.'" data-article_no="'.$article->article->no.'"><ion-icon name="pencil"></ion-icon> Assess</button>';
            })->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $answer = $request->toArray();
        $article_user = ArticleUser::where('article_id', $answer['article_id'])->where('user_id', auth()->user()->id)->first();

        foreach($answer['questionaire_id'] as $key => $value) {
            $questionaire_id = $value;
            $questionaire_answer = intval($answer['QA'.$value]);
            ArticleUserQuestionaire::Create([
                'article_user_id' => $article_user->id,
                'questionaire_id' => $questionaire_id,
                'score' => $questionaire_answer,
            ]);
        }
        $article_user->update([
            'is_assessed' => true,
        ]);

        return response()->json([
            'message' => 'Assessment has been submitted',
        ], 200);
    }

    public function assessedIndex()
    {
        $this->authorize('reviewer');
        $questionaires = Questionaire::all();
        $projects = Project::select('id','project_name')->whereHas('project_user', function($query) {
            $query->where('user_id', auth()->user()->id);
        })->get();
        return view('dashboard.reviewer.assessed', compact('questionaires', 'projects'));
    }

    public function assessedTable(Request $request)
    {
        $this->authorize('reviewer');
        $articles = [];
        if ($request->has('project_id') && $request->project_id != 'all') {
            $data = ArticleUser::with(['article' => function($query) use ($request) {
                $query->with('project')->whereHas('project', function($query) use ($request) {
                    $query->where('id', $request->project_id);
                });
            }])->where('user_id', auth()->user()->id)->where('is_assessed', true)->get();
    
            foreach ($data as $key => $value) {
                if ($value->article != null) {
                    $articles[] = $value;
                }
            }
        }
        else {
            $articles = ArticleUser::with(['article' => function($query) {
                $query->with('project');
            }])->where('user_id', auth()->user()->id)->where('is_assessed', true)->get()->sortBy('article.project.project_name');
        }

        return DataTables::of($articles)
            ->addColumn('no', function(ArticleUser $article){
                return $article->article->id.' - '.$article->article->no;
            })
            ->addColumn('title', function(ArticleUser $article){
                return $article->article->title;
            })
            ->addColumn('project_name', function(ArticleUser $article) {
                return $article->article->project->project_name;
            })
            ->addColumn('year', function(ArticleUser $article) {
                return $article->article->year;
            })
            ->addColumn('publication', function(ArticleUser $article){
                return $article->article->publication;
            })
            ->addColumn('authors', function(ArticleUser $article){
                return $article->article->authors;
            })
            ->addColumn('action', function(ArticleUser $article){
                $btn = '<button type="button" class="btn btn-warning text-white btn-sm me-2 aksi scoreArticle" id="scoreArticle" data-bs-toggle="modal" data-bs-target="#modalScore" data-id="' . $article->article->id . '" data-title="' . $article->article->title . '"><ion-icon name="stats-chart-outline"></ion-icon> Result</button>';
                $btn .= '<button class="btn btn-primary btn-sm" id="btn_assessment" data-bs-toggle="modal" data-bs-target="#exampleModal" data-article_id="'.$article->article->id.'" data-article_no="'.$article->article->no.'"><ion-icon name="create-outline"></ion-icon> Edit</button>';
                return $btn;
            })->rawColumns(['action'])
            ->toJson();
    }

    public function scoreReviewer(Request $request)
    {
        $this->authorize('reviewer');
        $score = Questionaire::with(['article_user_questionaire' => function($query) use ($request){
            $query->with(['articleUser' => function($query) use ($request){
                $query->with('user')->where('article_id', $request->article_id)->where('user_id', auth()->user()->id)->first();
            }]);
        }])->get();

        return $score;
    }
}
