<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectAdminController;
use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class, 'index']);

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->middleware('auth');

//Super Admin
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
//User Management
Route::get('/dashboard/user', [UserController::class, 'index'])->middleware('auth');
Route::get('/userTable', [UserController::class, 'userTable'])->middleware('auth');
Route::post('/addUser', [UserController::class, 'create'])->middleware('auth');
Route::put('/updateUser', [UserController::class, 'update'])->middleware('auth');
Route::delete('/deleteUser', [UserController::class, 'delete'])->middleware('auth');
//Project Management
Route::get('/dashboard/project', [ProjectController::class, 'index'])->middleware('auth');
Route::get('/projectTable', [ProjectController::class, 'projectTable'])->middleware('auth');
Route::post('/addProject', [ProjectController::class, 'store'])->middleware('auth');
Route::put('/updateProject', [ProjectController::class, 'update'])->middleware('auth');
Route::delete('/deleteProject', [ProjectController::class, 'delete'])->middleware('auth');
//ajax
Route::get('/findProjectUser', [ProjectController::class, 'findProjectUser'])->middleware('auth');

//Admin
Route::get('/dashboard/admin/project', [ProjectAdminController::class, 'index'])->middleware('auth');
Route::get('/dashboard/admin/project/{id}', [ProjectAdminController::class, 'show'])->middleware('auth')->name('project.show');
Route::get('/articleTable', [ArticleController::class, 'articleTable'])->middleware('auth')->name('article.table');
Route::get('/dashboard/admin/article/create', [ArticleController::class, 'create'])->middleware('auth')->name('article.create');
Route::post('/dashboard/admin/article/store', [ArticleController::class, 'store'])->middleware('auth')->name('article.store');
Route::delete('/deleteArticle', [ArticleController::class, 'delete'])->middleware('auth');
Route::get('/dashboard/admin/article/{id}/edit', [ArticleController::class, 'edit'])->middleware('auth')->name('article.edit');
Route::patch('/dashboard/admin/article/update', [ArticleController::class, 'update'])->middleware('auth')->name('article.update');
//////////////////////////////////////
// PENGOLAHAN DATA
/////////////////////////////////////
// sebelumnya bisa ikuti command dibawah
// cd ./python
// python run_app_flask.py

Route::get('/pengolahan-data', function () {
    return view('pengolahan_data_slr.home');
});

function getData(){
    $articles = DB::table('article')
                ->select('no', 'keywords', 'abstracts', 'year', 'authors', 'citing_new')
                ->get();

    $data = json_decode($articles, true);
    $result = [];

    $flag=0;
    foreach ($data as $row) {
        $flag++;
        // if($flag==44||$flag==44||$flag==48||$flag==49||$flag==50)continue;
        if($flag<=0) continue;
        // if($flag==38)echo $row['authors'];
        if($flag>60) break;
        $keywords = preg_split('/\s*[,;\/]\s*/', $row['keywords']);
        $authors = preg_split('/\s*[,;\/]\s*/', $row['authors']);

       
        foreach ($authors as $key => $author) {
            if (strlen($author) <= 3) {
                unset($authors[$key]);
            }
        }  
        sort($authors, SORT_NUMERIC);
          
        $citingNew = preg_split('/\s*[,;\/]\s*/', $row['citing_new']);
        foreach ($citingNew as $key => $citing) {
            if (strlen($citing) <= 1) {
                unset($citingNew[$key]);
            }
        }
        sort($citingNew, SORT_NUMERIC);

        $abstracts = $keywords;

        if(strlen($row['citing_new'])==1){
            $result[] = [$row['no'], $keywords, $abstracts,(string) $row['year'],$authors];
        }         
        else{
            $result[] = [$row['no'], $keywords, $abstracts,(string) $row['year'],$authors,  $citingNew];
        }    
    }
    return $result;
}

Route::get('/data/rank', function () {
    $result = getData();
    // transporse table
    // https://stackoverflow.com/questions/6297591/how-to-invert-transpose-the-rows-and-columns-of-an-html-table
    $response = Http::timeout(999999)->post('http://127.0.0.1:5000/data/rank', [
        'data' => 
            $result
            // [  
            //     [ "a1", ['a','b','c'],   ['a','b','c','k','l']    ,'1993',['p1','p2']                                              ]
            //     , [ "a2", ['c','d','e'],   ['a','c','d','e','m','n'],'1993',['p1','p3']                                              ]
            //     , [ "a3", ['f','g','h'],   ['c','d','f','g','h','o'],'1993',['p2','p4','p5']                                         ]
            //     , [ "a4", ['i','j'],       ['c','d','p','q']        ,'1994',['p3','p6']      ,['a1','a2']                            ]
            //     , [ "a5", ['dj','dk'],     ['a','dj','dk','m','r']  ,'1994',['p1','p7']      ,['a1','a2','a3']                       ]
            //     , [ "a6", ['d','ac','ad'], ['d','ac','ad','s','t']  ,'1994',['p8','p9']      ,['a1','a3']                            ]
            // ]
    ]);
    // return $response;
    // return json_decode($response);
    $authors = $response[0];
    $ranks =  $response[1][1];

    // Combine the authors and ranks into an array of arrays
    $author_ranks = array();
    for ($i = 0; $i < count($authors); $i++) {
        $author_ranks[] = array($authors[$i], $ranks[$i]);
    }

    // Sort the author-rank pairs based on the rank (ascending order)
    usort($author_ranks, function($a, $b) {
        return $a[1] - $b[1];
    });

    return view('pengolahan_data_slr.rank',  ['authors'=> $response[0],'ranktable' => $response[1][0],'rank' => $response[1][1],'author_ranks' => $author_ranks]);

});
Route::get('/data/graph', function () {
    $result = getData();

    $response = Http::post('http://127.0.0.1:5000/data/graph', [
        'data' => 
        $result
        // [  
        //     [ "a1", ['a','b','c'],   ['a','b','c','k','l']    ,'1993',['p1','p2']                                              ]
        //     , [ "a2", ['c','d','e'],   ['a','c','d','e','m','n'],'1993',['p1','p3']                                              ]
        //     , [ "a3", ['f','g','h'],   ['c','d','f','g','h','o'],'1993',['p2','p4','p5']                                         ]
        //     , [ "a4", ['i','j'],       ['c','d','p','q']        ,'1994',['p3','p6']      ,['a1','a2']                            ]
        //     , [ "a5", ['dj','dk'],     ['a','dj','dk','m','r']  ,'1994',['p1','p7']      ,['a1','a2','a3']                       ]
        //     , [ "a6", ['d','ac','ad'], ['d','ac','ad','s','t']  ,'1994',['p8','p9']      ,['a1','a3']                            ]
        // ]
    ]);
    return view('pengolahan_data_slr.graph', ['src' => "data:image/png;base64, $response"]);

});