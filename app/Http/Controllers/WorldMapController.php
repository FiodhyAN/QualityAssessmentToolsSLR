<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;

class WorldMapController extends Controller
{
    public function index()
    {
        $this->authorize('projectSummary');
        if (auth()->user()->is_superAdmin) {
            $projects = Project::select('id','project_name')->get();
        }
        else {
            $projects = Project::select('id','project_name')->whereHas('project_user', function($query) {
                $query->where('user_id', auth()->user()->id)->where('user_role','admin');
            })->get();
        }
        $id='worldmap';
        $name=$id;
        $name[0]=strtoupper($name[0]);
        return view('pengolahan_data_slr.worldmap', ['src' => "", 'author_ranks' => [], 'type' => $name, 'url'=>$id , 'projects' => $projects,'display' => 'none','id_project'=>'','world_map'=>[],"project_ajax"=>'',"topauthor"=> '',"outerauthor"=> '']);
    }

    public function process(Request $request)
    {
        $id='author';
        $author = $request->toArray();
        $validator = Validator::make($author, [
            'project' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $sum_top_author = (int) 20;
        $result = $this->getData($author['project']);
        set_time_limit(6000);
        $response = Http::timeout(6000)->post(
            'http://127.0.0.1:5000/data/' . $id . '/rankgraph',
            [
                'data' => $result
                ,
                'outer' => '0'
                ,
                'author-rank' => $sum_top_author
            ]
        );
        $authors = $response['authors'];
        $ranks = $response['ranks'];
        $title = $response['title'];
        $nodes_strength = $response['nodes_strength'];

        // make empty array world map
        $world_map = array();
        
        // Combine the authors and ranks into an array of arrays
        $author_ranks = array();
        for ($i = 0; $i < count($authors); $i++) {

            // cek apakah ada di world map
            if(!array_key_exists($title[$i],$world_map)){
                $world_map[$title[$i]]=1;
            }
            else{
                $world_map[$title[$i]]+=1;
            }
            $total_article=$this->get_total_article($authors[$i],$author['project']);
            $nodes_strength_val=$nodes_strength[$i];
            $author_ranks[] = array($i,$authors[$i], $ranks[$i], $title[$i],$total_article,$nodes_strength_val);
        }

           // convert world map to array of array
           $new_world_map = array();
           foreach ($world_map as $key => $value) {
               $color=$this->getDarkerHexColor('#C0C0C0', $value);
               $new_world_map[] = array($key,$color,$value);
           }
   
           // Sort the author-rank pairs based on the rank (ascending order)
           usort($author_ranks, function ($a, $b) {
               return $a[2] - $b[2];
           });
           //dapatkan data top 10 
           $author_ranks = array_slice($author_ranks, 0, $sum_top_author);
           $name=$id;
           $name[0]=strtoupper($name[0]);
           // data:image/png;base64, $image
           return redirect('worldmap')->with('worldmap', $new_world_map);
       }

    public function separate($keywords) {
        $newKeywords = [];
        foreach ($keywords as $keyword) {
          $regex = '/([A-Za-z]+\d+)/';
          preg_match_all($regex, $keyword, $matches);
          $newKeywords = array_merge($newKeywords, $matches[0]);
        }
        return $newKeywords;
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
            $citingNew=$this->separate($citingNew);

            $abstracts = $keywords;
            
            $result[] = [$row['no'], $keywords, $abstracts, (string) $row['year'], $authors, $citingNew,$row['title'],$row['nation_first_author']];

        }
        $result[] = ["dummyarticle", [], [], 'dummy year', ["dummywriter"],[],'title of dummywriter','dummy nation'];
        return $result;
    }

    public function getDarkerHexColor($color, $amount) {
        // konversi hex color ke RGB
        $r = hexdec(substr($color, 1, 2));
        $g = hexdec(substr($color, 3, 2));
        $b = hexdec(substr($color, 5, 2));
      
        // hitung nilai darker color
        $r = max($r - $amount, 0);
        $g = max($g - $amount, 0);
        $b = max($b - $amount, 0);
      
        // konversi kembali ke hex color
        $darkerColor = sprintf("#%02x%02x%02x", $r, $g, $b);
        return $darkerColor;
    }

    public function get_total_article($author_name,$projects) {
        $articles = Article::select('no', 'keywords', 'abstracts', 'year', 'authors', 'citing_new','title','nation_first_author')->where('project_id', '=', $projects)->where('authors', 'like', '%' . $author_name . '%')
            ->get();
        $total_article=count($articles);
        return $total_article;
    }


}