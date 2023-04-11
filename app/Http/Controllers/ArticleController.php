<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Yajra\DataTables\DataTables;
use App\Imports\ArticleImport;
use App\Models\ArticleUser;
use App\Models\ArticleUserQuestionaire;
use App\Models\Questionaire;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ArticleController extends Controller
{
    public function articleTable($id)
    {
        $this->authorize('admin');
        $articles = Article::select('id', 'no', 'title', 'year', 'publication', 'authors')->where('project_id', $id)->get();
        return DataTables::of($articles)
            ->addColumn('no', function (Article $article) {
                return $article->id . ' - ' . $article->no;
            })
            ->addColumn('title', function (Article $article) {
                return '<span style="white-space:normal">'.$article->title.'</span>';
            })
            ->addColumn('year', function (Article $article) {
                return $article->year;
            })
            ->addColumn('publication', function (Article $article) {
                return '<span style="white-space:normal">'.$article->publication.'</span>';
            })
            ->addColumn('authors', function (Article $article) {
                return '<span style="white-space:normal">'.$article->authors.'</span>';
            })
            ->addColumn('action', function (Article $article) use ($id) {
                $btn = '<button type="button" class="btn btn-warning text-white btn-sm me-2 aksi scoreArticle" id="scoreArticle" data-bs-toggle="modal" data-bs-target="#modalScore" data-id="' . $article->id . '" data-title="' . $article->title . '"><ion-icon name="stats-chart-outline"></ion-icon> Score</button>';
                $btn .= '<a href="/dashboard/admin/article/' . $article->id . '/edit?pid=' . $id . '"><button type="button" class="btn btn-primary btn-sm aksi"><ion-icon name="create-outline"></ion-icon> Edit</button></a>';
                $btn .= '<button type="button" class="btn btn-danger btn-sm ms-2 aksi deleteArticle" data-id="' . $article->id . '"><ion-icon name="trash-outline"></ion-icon> Delete</button>';
                return $btn;
            })
            ->rawColumns(['no','title', 'publication', 'authors', 'action'])
            ->toJson();
    }

    public function assignmentTable($id)
    {
        $this->authorize('admin');
        $users = User::with(['article_user' => function ($query) use ($id) {
                    $query->whereHas('article', function ($query) use ($id) {
                        $query->where('project_id', $id);
                    });
                }])->where('id', '!=', auth()->user()->id)->where('is_superadmin', false)->whereHas('project_user', function ($query) use ($id) {
                    $query->where('project_id', $id)->where('user_role', 'reviewer');
                })->get();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('name', function (User $user) {
                return $user->name;
            })
            ->addColumn('article', function (User $user) {
                if(count($user->article_user) == 0 || $user->article_user[0]->article == null){
                    return '<span class="badge alert-danger text">No Article Assigned</span>';
                } else {
                    return '<span class="badge alert-primary">'.count($user->article_user).' Article(s) Assigned</span>';
                }
            })
            // ->addColumn('id_no', function (User $user) {
            //     if (count($user->article_user) == 0 || $user->article_user[0]->article == null) {
            //         return false;
            //     } else {
            //         $id_no = '';
            //         foreach ($user->article_user as $value) {
            //             $id_no .= $value->article->id . ' - ' . $value->article->no . '<br>';
            //         }
            //         return $id_no;
            //     }
            // })
            // ->addColumn('title', function (User $user) {
            //     if (count($user->article_user) == 0 || $user->article_user[0]->article == null) {
            //         return false;
            //     } else {
            //         $title = '';
            //         foreach ($user->article_user as $value) {
            //             $title .= $value->article->title . '<br>';
            //         }
            //         return $title;
            //     }
            // })
            // ->addColumn('assessed', function (User $user) {
            //     if (count($user->article_user) == 0 || $user->article_user[0]->article == null) {
            //         return false;
            //     } else {
            //         $assessed = '';
            //         foreach ($user->article_user as $value) {
            //             if ($value->is_assessed == true) {
            //                 $assessed .= '<span class="badge alert-success">Assessed</span><br>';
            //             } else {
            //                 $assessed .= '<span class="badge alert-danger">Not Assessed</span><br>';
            //             }
            //         }
            //         return $assessed;
            //     }
            // })
            ->addColumn('action', function (User $user) use ($id) {
                if (count($user->article_user) == 0 || $user->article_user[0]->article == null)
                {
                    $btn = '<a href="/dashboard/admin/assign?pid=' . $id . '&uid=' . $user->id . '">
                                <button type="button" class="btn btn-sm btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-check">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <polyline points="17 11 19 13 23 9"></polyline>
                                </svg> Assign</button>
                            </a>';
                    $btn .= '<button disabled type="button" id="showArticle" class="btn btn-sm btn-primary">
                                <ion-icon name="eye-sharp"></ion-icon> Show
                            </button>';  
                }
                else {
                    $btn = '<a href="/dashboard/admin/assign?pid=' . $id . '&uid=' . $user->id . '">
                                <button type="button" class="btn btn-sm btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-check">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <polyline points="17 11 19 13 23 9"></polyline>
                                </svg> Assign</button>
                            </a>';
                    $btn .= '<a href="javascript:;"
                                <button type="button" id="showArticle" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#articleModal" data-name="'.$user->name.'">
                                <ion-icon name="eye-sharp"></ion-icon> Show</button>
                            </a>';  
                }
                return $btn;
            })
            // ->rawColumns(['id_no', 'title', 'assessed', 'action'])
            ->rawColumns(['action', 'article'])
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
                $file_path = public_path('articles/' . $article->file);
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
        $articleUser = ArticleUser::where('article_id', $request->id)->get();
        foreach ($articleUser as $au) {
            if($au->is_assessed == true)
            {
                return json_encode(['error' => 'Article has been assessed, cannot be deleted!']);
            }
        }
        ArticleUser::where('article_id', $request->id)->delete();
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

    public function articleScore(Request $request)
    {
        $this->authorize('admin');
        $score = Questionaire::with(['article_user_questionaire' => function($query) use ($request){
            $query->with(['articleUser' => function($query) use ($request){
                $query->with('user')->where('article_id', $request->article_id);
            }]);
        }])->get();
        return $score;
    }

    public function findArticleUser(Request $request)
    {
        $this->authorize('admin');
        
    }
}
