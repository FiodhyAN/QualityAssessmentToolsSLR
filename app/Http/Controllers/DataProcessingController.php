<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DataProcessingController extends Controller
{
    public function pengolahan_data() {
        return view('pengolahan_data_slr.home');
    }   

    public function gambar_graph() {
        $articles = DB::table('graphimage')
                    ->select('base64code')
                    ->get();
    
        $data = json_decode($articles, true);
        $response=$data[0]['base64code'];
        return view('graph', ['src' => "data:image/png;base64, $response"]);
    }

    public function my_image() {
        $articles = DB::table('graphimage')
                    ->select('base64code')
                    ->get();
    
        $data = json_decode($articles, true);
        $response=$data[0]['base64code'];
    
        // Create an HTTP response with the image data
        $headers = [
            'Content-Type' => 'image/png',
        ];
        $statusCode = 200;
        $content = base64_decode($response);
        $response = new Response($content, $statusCode, $headers);
    
        // Return the HTTP response
        return $response;
    }


    public function getData(){
        $articles = DB::table('articles')
                    ->select('no', 'keywords', 'abstracts', 'year', 'authors', 'citing_new')
                    ->get();
    
        $data = json_decode($articles, true);
        $result = [];
    
        $flag=0;
        foreach ($data as $row) {
            $flag++;
            if($flag<=0) continue;
            if($flag>60) break;
            $keywords = preg_split('/\s*[,;\/]\s*/', $row['keywords']);
            $authors = preg_split('/\s*[,;\/]\s*/', $row['authors']);
            sort($authors, SORT_NUMERIC);  
            $citingNew = preg_split('/\s*[,;\/]\s*/', $row['citing_new']);
            sort($citingNew, SORT_NUMERIC);   
            $abstracts = $keywords;
            $result[] = [$row['no'], $keywords, $abstracts,(string) $row['year'],$authors,  $citingNew];
  
        }
        $result[] = ["dummywriter", [], [],[],["dummywriter"]];
        return $result;
    }


    public function data_rank() {
        $result = $this->getData();
        // transporse table
        // https://stackoverflow.com/questions/6297591/how-to-invert-transpose-the-rows-and-columns-of-an-html-table
        set_time_limit(6000);
        $response = Http::timeout(6000)->post('http://127.0.0.1:5000/data/rank', [
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
            ,'outer'=>true
            ,'author-rank'=>10
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
    
    }

    public function data_graph() {
        $result = $this->getData();
        set_time_limit(6000);
        $response =  Http::timeout(6000)->post('http://127.0.0.1:5000/data/graph', [
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
            ,'outer'=>true
            ,'author-rank'=>10
        ]);
        return view('pengolahan_data_slr.graph', ['src' => "data:image/png;base64, $response"]);
    
    }

}
