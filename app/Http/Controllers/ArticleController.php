<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\ProjectUser;
use Yajra\DataTables\DataTables;
use App\Imports\ArticleImport;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ArticleController extends Controller
{
    public function articleTable($id)
    {
        $this->authorize('admin');
        $articles = Article::where('project_id', $id)->get();
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
            ->addColumn('action', function(Article $article) use ($id) {
                $btn = '<button type="button" class="btn btn-warning text-white btn-sm me-2 aksi scoreArticle" data-id="'.$article->id.'"><ion-icon name="stats-chart-outline"></ion-icon> Score</button>';
                $btn .= '<a href="/dashboard/admin/article/'.$article->id.'/edit?pid='.$id.'"><button type="button" class="btn btn-primary btn-sm aksi"><ion-icon name="create-outline"></ion-icon> Edit</button></a>';
                $btn .= '<button type="button" class="btn btn-danger btn-sm ms-2 aksi deleteArticle" data-id="'.$article->id.'"><ion-icon name="trash-outline"></ion-icon> Delete</button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create()
    {
        $this->authorize('admin');
        return view('dashboard.admin.article.create', [
            'project_id' => request()->id
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('admin');
        // return $request;
        $request->validate([
            'kode_artikel' => 'required',
            'file' => 'mimes:pdf|nullable',
            'title' => 'required',
            'publication' => 'required',
            'year' => 'required',
            'authors' => 'required',
            'language' => 'required',
            'article_type' => 'required',
            'publisher' => 'required',
            'cited' => 'required',
            'cited_gs' => 'required',
            'cited_other' => 'required',
            'keyword' => 'required',
            'edatabase' => 'required',
        ]);
        //save file to database
        if ($request->file('file') != null) {
            $file = $request->file('file');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('articles'), $file_name);
        }
        //save data to database
        $article = Article::create([
            'no' => $request->kode_artikel,
            'file' => $file_name ?? null,
            'title' => $request->title,
            'publication' => $request->publication,
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
            'citing' => $request->cited_other,
            'keyword' => $request->keyword,
            'edatabase' => $request->edatabase,
            'edatabase_2' => $request->edatabase2 ?? null,
            'project_id' => $request->project_id,
        ]);
        return redirect()->route('project.show',  $request->project_id)->with('success', 'Article successfully added!');
    }

    public function edit($id)
    {
        $this->authorize('admin');
        $article = Article::find($id);
        // return $article;
        return view('dashboard.admin.article.edit', [
            'article' => $article,
            'project_id' => request()->pid
        ]);
    }

    public function update(Request $request)
    {
        $this->authorize('admin');
        // return $request;
        $request->validate([
            'kode_artikel' => 'required',
            'file' => 'mimes:pdf|nullable',
            'title' => 'required',
            'publication' => 'required',
            'year' => 'required',
            'authors' => 'required',
            'language' => 'required',
            'article_type' => 'required',
            'publisher' => 'required',
            'cited' => 'required',
            'cited_gs' => 'required',
            'cited_other' => 'required',
            'keyword' => 'required',
            'edatabase' => 'required',
        ]);

        $article = Article::find($request->article_id);
        if ($request->file('file') != null) {
            if ($article->file != null) {
                $file_path = public_path('articles/'.$article->file);
                $file_delete = File::delete($file_path);
            }

            $file = $request->file('file');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('articles'), $file_name);
        }

        $article->update([
            'no' => $request->kode_artikel,
            'file' => $file_name ?? $article->file,
            'title' => $request->title ?? $article->title,
            'publication' => $request->publication ?? $article->publication,
            'index' => $request->index ?? $article->index,
            'quartile' => $request->quartile ?? $article->quartile,
            'year' => $request->year ?? $article->year,
            'authors' => $request->authors ?? $article->authors,
            'abstracts' => $request->abstract ?? $article->abstracts,
            'keywords' => $request->keywords ?? $article->keywords,
            'language' => $request->language ?? $article->language,
            'type' => $request->article_type ?? $article->type,
            'publisher' => $request->publisher ?? $article->publisher,
            'references_ori' => $request->references_ori ?? $article->references_ori,
            'references_filter' => $request->references_filter ?? $article->references_filter,
            'cited' => $request->cited ?? $article->cited,
            'cited_gs' => $request->cited_gs ?? $article->cited_gs,
            'citing_new' => $request->cited_other ?? $article->citing_new,
            'keyword' => $request->keyword ?? $article->keyword,
            'edatabase' => $request->edatabase ?? $article->edatabase,
            'edatabase_2' => $request->edatabase2 ?? $article->edatabase_2,
        ]);
        return redirect()->route('project.show',  $article->project_id)->with('success', 'Article successfully updated!');
    }

    public function delete(Request $request)
    {
        $this->authorize('admin');
        $article = Article::find($request->id);
        return $article->delete();
    }

    public function storeExcel(Request $request)
    {
        $this->authorize('admin');
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx'
        ]);
        $file = $request->file('excel_file');

        Excel::import(new ArticleImport($request->project_id), $file);

        return response()->json(['success' => 'Excel data imported successfully.']);
    }

    public function downloadExcel()
    {
        $this->authorize('admin');
        return response()->download(public_path('articles/TemplateSLR.xlsx'));
    }
}
