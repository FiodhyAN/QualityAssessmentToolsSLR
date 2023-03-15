<?php

namespace App\Http\Controllers;

use App\Models\ArticleUser;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        $articles = ArticleUser::with(['article' => function($query) {
            $query->with('project');
        }])->where('user_id', auth()->user()->id)->where('is_assessed', false)->get();
        return $articles;
        return view('dashboard.reviewer.assessment', compact('articles'));
    }

    public function assessmentTable()
    {
        
    }
}
