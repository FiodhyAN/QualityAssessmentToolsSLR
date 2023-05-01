<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Project;

class DataProcessingController extends Controller
{
    public function pengolahan_data()
    {
        $this->authorize('projectSummary');
        $graph = DB::table('data_graph')
            ->select('base64code')
            ->get();
        $data = json_decode($graph, true);
        $graph = $data[0]['base64code'];

        $rank_meta = DB::table('data_rank')
            ->select('json')
            ->get();
        $data = json_decode($rank_meta, true);
        $rank_meta = $data[0]['json'];
        $data_rank = json_decode($rank_meta, true);

        $author_ranks = array();
        for ($i = 0; $i < count($data_rank["author"]); $i++) {
            $author_ranks[] = array($data_rank["author"][$i], $data_rank["ranks"][$i]);
        }

        return view('pengolahan_data_slr.home', ['src' => "data:image/png;base64, $graph", 'author_ranks' => $author_ranks]);
    }

    public function gambar_graph()
    {
        $this->authorize('projectSummary');
        $articles = DB::table('data_graph')
            ->select('base64code')
            ->get();

        $data = json_decode($articles, true);
        $response = $data[0]['base64code'];
        return view('graph', ['src' => "data:image/png;base64, $response"]);
    }

    public function my_image()
    {
        $this->authorize('projectSummary');
        $articles = DB::table('graphimage')
            ->select('base64code')
            ->get();

        $data = json_decode($articles, true);
        $response = $data[0]['base64code'];

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


    public function getData($projects)
    {
        $this->authorize('projectSummary');
        $articles = Article::select('no', 'keywords', 'abstracts', 'year', 'authors', 'citing_new','title','nation_first_author')->where('project_id', '=', $projects)
            ->get();

        $data = json_decode($articles, true);
        $result = [];

        $flag = 0;
        foreach ($data as $row) {
            // $flag++;
            // if ($flag <= 0)
            //     continue;
            // if ($flag > 60)
            //     break;
            $keywords = preg_split('/\s*[,;\/]\s*/', $row['keywords']);
            $authors = preg_split('/\s*[,;\/]\s*/', $row['authors']);
            sort($authors, SORT_NUMERIC);
            $citingNew = preg_split('/\s*[,;\/]\s*/', $row['citing_new']);
            sort($citingNew, SORT_NUMERIC);
            $abstracts = $keywords;
            $result[] = [$row['no'], $keywords, $abstracts, (string) $row['year'], $authors, $citingNew,$row['title'],$row['nation_first_author']];

        }
        $result[] = ["dummyarticle", [], [], 'dummy year', ["dummywriter"],[],'title of dummywriter','dummy nation'];
        return $result;
    }


    public function data_rank($id)
    {
        $this->authorize('projectSummary');
        $sum_top_author = 10;
        $result = $this->getData(1);
        // transporse table
        // https://stackoverflow.com/questions/6297591/how-to-invert-transpose-the-rows-and-columns-of-an-html-table
        set_time_limit(6000);
        $response = Http::timeout(6000)->post('http://127.0.0.1:5000/data/' . $id . '/rank', [
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
            ,
            'outer' => true
            ,
            'author-rank' => $sum_top_author
        ]);
        // return $response;
        // return json_decode($response);
        $authors = $response[0];
        $ranks = $response[1][1];
        $title = $response[2];

        // Combine the authors and ranks into an array of arrays
        $author_ranks = array();
        for ($i = 0; $i < count($authors); $i++) {
            $author_ranks[] = array($i,$authors[$i], $ranks[$i], $title[$i]);
        }

        // Sort the author-rank pairs based on the rank (ascending order)
        usort($author_ranks, function ($a, $b) {
            return $a[2] - $b[2];
        });
        //dapatkan data top 10 
        $author_ranks = array_slice($author_ranks, 0, $sum_top_author);
        return view('pengolahan_data_slr.rank', ['authors' => $response[0], 'ranktable' => $response[1][0], 'rank' => $response[1][1], 'author_ranks' => $author_ranks]);

    }

    public function data_graph($id)
    {
        $sum_top_author = 10;
        $result = $this->getData(1);
        set_time_limit(6000);
        $response = Http::timeout(6000)->post('http://127.0.0.1:5000/data/' . $id . '/graph', [
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
            ,
            'outer' => true
            ,
            'author-rank' => $sum_top_author
        ]);
        return view('pengolahan_data_slr.graph', ['src' => "data:image/png;base64, $response"]);

    }

    public function meta_data($id)
    {
        $this->authorize('projectSummary');
        if (auth()->user()->is_superAdmin) {
            $projects = Project::select('id','project_name')->get();
        }
        else {
            $projects = Project::select('id','project_name')->whereHas('project_user', function($query) {
                $query->where('user_id', auth()->user()->id);
            })->get();
        }
        $name=$id;
        $name[0]=strtoupper($name[0]);
        return view('pengolahan_data_slr.metadata', ['src' => "", 'author_ranks' => [], 'type' => $name, 'url'=>$id , 'projects' => $projects,'display' => 'none','id_project'=>'']);
    }

    public function proses_meta_data(Request $request, $id)
    {
        $this->authorize('projectSummary');
        if (auth()->user()->is_superAdmin) {
            $projects = Project::select('id','project_name')->get();
        }
        else {
            $projects = Project::select('id','project_name')->whereHas('project_user', function($query) {
                $query->where('user_id', auth()->user()->id);
            })->get();
        }
        

        $author = $request->toArray();
        if ($author['outer-author'] == '0') {
            $author['outer-author'] = false;
        } else {
            $author['outer-author'] = true;
        }

        $sum_top_author = (int) $author['top-author'];
        $result = $this->getData($author['project']);
        // transporse table
        // https://stackoverflow.com/questions/6297591/how-to-invert-transpose-the-rows-and-columns-of-an-html-table
        set_time_limit(6000);
        $response = Http::timeout(6000)->post(
            'http://127.0.0.1:5000/data/' . $id . '/rankgraph',
            // 'https://lokerkita640.pythonanywhere.com//data/' . $id . '/rankgraph',
            [
                'data' => $result
                // 'data' => [
                //     ["a1", ['a', 'b', 'c'],   ['a', 'b', 'c', 'k', 'l'], '1993', ['p1', 'p2'],'title of a1','nation of p1'], 
                //     ["a2", ['c', 'd', 'e'],   ['a', 'c', 'd', 'e', 'm', 'n'], '1993', ['p1', 'p3'],'title of a2','nation of p1'],
                //     ["a3", ['f', 'g', 'h'],   ['c', 'd', 'f', 'g', 'h', 'o'], '1993', ['p2', 'p4', 'p5'],'title of a3','nation of p2'], 
                //     ["a4", ['i', 'j'],        ['c', 'd', 'p', 'q'], '1994', ['p3', 'p6'], ['a1', 'a2'], 'title of a4', 'nation of p3'], 
                //     ["a5", ['dj', 'dk'],      ['a', 'dj', 'dk', 'm', 'r'], '1994', ['p1', 'p7'], ['a1', 'a2', 'a3'], 'title of a5','nation of p1'], 
                //     ["a6", ['d', 'ac', 'ad'], ['d', 'ac', 'ad', 's', 't'], '1994', ['p8', 'p9'], ['a1', 'a3'], 'title of a6','nation of p8'],
                // ]
                ,
                'outer' => $author['outer-author']
                ,
                'author-rank' => $sum_top_author
            ]
        );

        $image = $response['graph'];
        $image =utf8_decode($image);
        $authors = $response['authors'];
        $ranks = $response['ranks'];
        $title = $response['title'];
        
        

        // Combine the authors and ranks into an array of arrays
        $author_ranks = array();
        for ($i = 0; $i < count($authors); $i++) {
            $author_ranks[] = array($i,$authors[$i], $ranks[$i], $title[$i]);
        }

        // Sort the author-rank pairs based on the rank (ascending order)
        usort($author_ranks, function ($a, $b) {
            return $a[2] - $b[2];
        });
        //dapatkan data top 10 
        $author_ranks = array_slice($author_ranks, 0, $sum_top_author);
        $name=$id;
        $name[0]=strtoupper($name[0]);
        return view('pengolahan_data_slr.metadata', ['src' => "data:image/png;base64, $image", 'author_ranks' => $author_ranks, 'type' => $name, 'url'=>$id ,'projects' => $projects,'display' => 'block','id_project'=>$author['project']]);
    }

}