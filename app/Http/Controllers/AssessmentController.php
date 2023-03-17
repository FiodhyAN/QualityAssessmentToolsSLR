<?php

namespace App\Http\Controllers;

use App\Models\ArticleUser;
use App\Models\ArticleUserQuestionaire;
use App\Models\Questionaire;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AssessmentController extends Controller
{
    public function index()
    {
        $this->authorize('reviewer');
        $questionaires = Questionaire::all();
        return view('dashboard.reviewer.assessment', compact('questionaires'));
    }

    public function assessmentTable()
    {
        $this->authorize('reviewer');
        $articles = ArticleUser::with(['article' => function($query) {
            $query->with('project');
        }])->where('user_id', auth()->user()->id)->where('is_assessed', false)->get()->sortBy('article.project.project_name');

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
        // $data = $request->toArray();
        // return $data['QA'.$data['questionaire_id'][2]];

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
}
