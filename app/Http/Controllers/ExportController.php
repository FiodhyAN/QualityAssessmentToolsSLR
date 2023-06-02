<?php

namespace App\Http\Controllers;

use App\Exports\ResultExport;
use App\Models\Project;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function export($id)
    {
        $this->authorize('admin');
        $project_id = decrypt($id);
        $project = Project::findOrFail($project_id);
        $filename = 'Score For Project ' . $project->project_name . '.xlsx';
        return (new ResultExport($project->id))->download($filename);
    }
}
