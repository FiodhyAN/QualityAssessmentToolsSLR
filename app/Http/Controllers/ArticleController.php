<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function create()
    {
        $this->authorize('admin');
        return view('dashboard.admin.article.create');
    }

    public function store(Request $request)
    {
        $this->authorize('admin');
        $request->validate([
            'no' => 'required',
            'file' => 'mimes:pdf',
            'title' => 'required',
            'year' => 'required',
            'publication' => 'required',
            'authors' => 'required',
            'language' => 'required',
            'type' => 'required',
            'publisher' => 'required',
            'cited' => 'required',
            'cited_gs' => 'required',
            'citing_new' => 'required',
            'keyword' => 'required',
            'edatabase' => 'required',
        ]);
        //save file to database
        $file = $request->file('file');
        $file_name = $file->getClientOriginalName();
        $file->move(public_path('articles'), $file_name);
        //save data to database
        Article::create([
            'no' => $request->kode_artikel,
            'file' => $file_name ?? null,
            'title' => $request->title,
            'index' => $request->index ?? null,
            'quartile' => $request->quartile ?? null,
            'year' => $request->year,
            'authors' => $request->authors,
            'abstracts' => $request->abstract ?? null,
            'keywords' => $request->keywords ?? null,
            'language' => $request->language,
            'type' => $request->article_type,
            'publisher' => $request->publisher ?? null,
            'references_ori' => $request->references_ori ?? null,
            'references_filter' => $request->references_filter ?? null,
            'cited' => $request->cited,
            'cited_gs' => $request->cited_gs,
            'citing_new' => $request->cited_other,
            'keyword' => $request->keyword,
            'edatabase' => $request->edatabase,
            'edatabase2' => $request->edatabase2 ?? null,
        ]);
        return view('dashboard.admin.article.index')->with('success', 'Article successfully added!');
    }
}
