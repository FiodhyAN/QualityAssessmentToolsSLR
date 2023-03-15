<?php

namespace App\Http\Controllers;

use App\Models\ArticleUser;
use App\Models\Questionaire;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AssessmentController extends Controller
{
    public function index()
    {
        $this->authorize('reviewer');
        $questionaire = Questionaire::all();
        return view('dashboard.reviewer.assessment', compact('questionaire'));
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
}
