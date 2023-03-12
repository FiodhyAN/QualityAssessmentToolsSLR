<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleUser;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AssignReviewerController extends Controller
{
    public function index()
    {
        $this->authorize('admin');
        return view('dashboard.admin.article.assign', [
            'project_id' => request()->pid,
            'user_id' => request()->uid
        ]);
    }

    public function articleNotAssignTable(Request $request)
    {
        $data = Article::with(['article_user' => function($query) use ($request) {
            $query->where('user_id', $request->user_id);
        }])->where('project_id', $request->project_id)->get();
        $article = [];
        foreach ($data as $key => $value) {
            if (count($value->article_user) == 0) {
                $article[] = $value;
            }
        }
        return DataTables::of($article)
            ->addColumn('action', function(Article $article){
                //add checkbox
                return '<input type="checkbox" name="article_id[]" class="cb_child" value="'.$article->id.'">';
            })->rawColumns(['action'])
            ->addColumn('no', function(Article $article){
                return $article->no;
            })
            ->addColumn('title', function(Article $article){
                return $article->title;
            })
            ->addColumn('year', function(Article $article){
                return $article->year;
            })
            ->addColumn('publication', function(Article $article){
                return $article->publication;
            })
            ->addColumn('authors', function(Article $article){
                return $article->authors;
            })
            ->toJson();
    }

    public function articleAssignTable(Request $request)
    {
        $this->authorize('admin');
        $data = ArticleUser::with(['article' => function($query) use ($request){
            $query->where('project_id', $request->project_id);
        }])->where('user_id', $request->user_id)->get();
        $articles = [];
        foreach ($data as $key => $value) {
            if ($value->article != null) {
                $articles[$key] = $value->article;
            }
        }
        return DataTables::of($articles)
            ->addColumn('action', function(Article $article){
                //add checkbox
                return '<input type="checkbox" name="article_id[]" class="cb_child_assign" value="'.$article->id.'">';
            })->rawColumns(['action'])
            ->addColumn('no', function(Article $article){
                return $article->no;
            })
            ->addColumn('title', function(Article $article){
                return $article->title;
            })
            ->addColumn('year', function(Article $article){
                return $article->year;
            })
            ->addColumn('publication', function(Article $article){
                return $article->publication;
            })
            ->addColumn('authors', function(Article $article){
                return $article->authors;
            })
            ->addColumn('action', function(Article $article){
                //add checkbox
                return '<input type="checkbox" name="article_id[]" value="'.$article->id.'">';
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
