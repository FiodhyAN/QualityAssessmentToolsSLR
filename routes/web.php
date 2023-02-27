<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
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
Route::post('/addUser', [UserController::class, 'create'])->middleware('auth');
Route::put('/updateUser', [UserController::class, 'update'])->middleware('auth');
Route::delete('/deleteUser', [UserController::class, 'delete'])->middleware('auth');
//Project Management
Route::get('/dashboard/project', [ProjectController::class, 'index'])->middleware('auth');
Route::post('/addProject', [ProjectController::class, 'store'])->middleware('auth');
Route::put('/updateProject', [ProjectController::class, 'update'])->middleware('auth');
Route::delete('/deleteProject', [ProjectController::class, 'delete'])->middleware('auth');
//ajax
Route::get('/findProjectUser', [ProjectController::class, 'findProjectUser'])->middleware('auth');

//////////////////////////////////////
// PENGOLAHAN DATA
/////////////////////////////////////
// sebelumnya bisa ikuti command dibawah
// cd ./python
// python run_app_flask.py

Route::get('/pengolahan-data', function () {
    return view('pengolahan_data_slr.home');
});

Route::get('/data/rank', function () {
    // transporse table
    // https://stackoverflow.com/questions/6297591/how-to-invert-transpose-the-rows-and-columns-of-an-html-table
    $response = Http::post('http://127.0.0.1:5000/data/rank', [
        'data' => [  
            [ "a1", ['a','b','c'],   ['a','b','c','k','l']    ,'1993',['p1','p2']                                              ]
            , [ "a2", ['c','d','e'],   ['a','c','d','e','m','n'],'1993',['p1','p3']                                              ]
            , [ "a3", ['f','g','h'],   ['c','d','f','g','h','o'],'1993',['p2','p4','p5']                                         ]
            , [ "a4", ['i','j'],       ['c','d','p','q']        ,'1994',['p3','p6']      ,['a1','a2']                            ]
            , [ "a5", ['dj','dk'],     ['a','dj','dk','m','r']  ,'1994',['p1','p7']      ,['a1','a2','a3']                       ]
            , [ "a6", ['d','ac','ad'], ['d','ac','ad','s','t']  ,'1994',['p8','p9']      ,['a1','a3']                            ]
            ]
    ]);
    // return json_decode($response);
    return view('pengolahan_data_slr.rank', ['rank' => json_decode($response)]);

});
Route::get('/data/graph', function () {
    $response = Http::post('http://127.0.0.1:5000/data/graph', [
        'data' => [  
            [ "a1", ['a','b','c'],   ['a','b','c','k','l']    ,'1993',['p1','p2']                                              ]
            , [ "a2", ['c','d','e'],   ['a','c','d','e','m','n'],'1993',['p1','p3']                                              ]
            , [ "a3", ['f','g','h'],   ['c','d','f','g','h','o'],'1993',['p2','p4','p5']                                         ]
            , [ "a4", ['i','j'],       ['c','d','p','q']        ,'1994',['p3','p6']      ,['a1','a2']                            ]
            , [ "a5", ['dj','dk'],     ['a','dj','dk','m','r']  ,'1994',['p1','p7']      ,['a1','a2','a3']                       ]
            , [ "a6", ['d','ac','ad'], ['d','ac','ad','s','t']  ,'1994',['p8','p9']      ,['a1','a3']                            ]
            ]
    ]);
    return view('pengolahan_data_slr.graph', ['src' => "data:image/png;base64, $response"]);
});
