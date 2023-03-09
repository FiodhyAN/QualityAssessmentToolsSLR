<?php

namespace App\Imports;

use App\Models\Article;
use Maatwebsite\Excel\Concerns\ToModel;

class ArticleImport implements ToModel, WithStartRow
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function __construct($project_id)
    {
        $this->project_id = $project_id;
    }
    
    public function model(array $row)
    {
        return new Article([
            'no' => $row[0],
            'title' => $row[1],
            'year' => $row[2],
            'authors' => $row[3],
            'abstracts' => $row[4],
            'keywords' => $row[5],
            'language' => $row[6],
            'type' => $row[7],
            'publisher' => $row[8],
            'references_ori' => $row[9],
            'references_filter' => $row[10],
            'cited' => $row[11],
            'cited_gs' => $row[12],
            'citing_new' => $row[13],
            'keyword' => $row[14],
            'edatabase' => $row[15],
            'edatabase_2' => $row[16],
            'nation_first_author' => $row[17],
            'project_id' => $this->project_id,
        ]);
    }
}
