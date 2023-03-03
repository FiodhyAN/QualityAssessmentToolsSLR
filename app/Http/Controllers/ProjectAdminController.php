<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectUser;
use App\Models\Project;
use App\Models\Article;
use App\Models\User;
use Yajra\DataTables\DataTables;

class ProjectAdminController extends Controller
{
    public function index()
    {
        $this->authorize('admin');
        $projects = ProjectUser::with('project')->where('user_id', auth()->user()->id)->get();
        return view('dashboard.admin.project', compact('projects'));
    }

    public function show(Project $projects)
    {
        $this->authorize('admin');
        $users = User::with(['article_user' => function($query) {
            $query->with('article');
        }])->where('id', '!=', auth()->user()->id)->where('is_superadmin', false)->get();
        $project = ProjectUser::with('project')->where('user_id', auth()->user()->id)->where('project_id', $projects->id)->first();
        return view('dashboard.admin.article.index', compact('project', 'users'));
    }

    public function assessment()
    {
        $this->authorize('admin');
        $users = User::with(['article_user' => function($query) {
            $query->with('article');
        }])->where('id', '!=', auth()->user()->id)->where('is_superadmin', false)->get();
        return view('dashboard.admin.assignment', compact('users'));
    }

    public function articleTable()
    {
        $this->authorize('admin');
        $articles = Article::all();
        return DataTables::of($articles)
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
                $btn = '<button type="button" class="btn btn-warning text-white btn-sm me-2 aksi scoreArticle" data-id="'.$article->id.'"><ion-icon name="stats-chart-outline"></ion-icon> Score</button>';
                $btn .= '<button type="button" class="btn btn-primary btn-sm aksi" data-toggle="modal" data-bs-target="#modalEdit" data-id="'.$article->id.'" data-title="'.$article->title.'" data-year="'.$article->year.'" data-publication="'.$article->publication.'" data-authors="'.$article->author.'"><ion-icon name="create-outline"></ion-icon> Edit</button>';
                $btn .= '<button type="button" class="btn btn-danger btn-sm ms-2 aksi deleteArticle" data-id="'.$article->id.'"><ion-icon name="trash-outline"></ion-icon> Delete</button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create()
    {
        $this->authorize('admin');
        return view('dashboard.admin.article.create');
    }
}
