@extends('layouts.main')

@section('container')
    <h1>Summary</h1>
    <hr>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="project_table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                @if (auth()->user()->is_admin == true)
                                    <td>{{ $project->project->project_name }}</td>
                                @else
                                    <td>{{ $project->project_name }}</td>
                                @endif
                                @if (auth()->user()->is_admin == true)
                                    <td>
                                        <a href="/dashboard/projectSummary?pid={{ $project->project_id }}" class="btn btn-secondary"><ion-icon name="bar-chart-outline"></ion-icon> Summary</a>
                                    </td>
                                @else
                                    <td>
                                        <a href="/dashboard/projectSummary?pid={{ $project->id }}" class="btn btn-secondary"><ion-icon name="bar-chart-outline"></ion-icon> Summary</a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#project_table').DataTable();
        });
    </script>
@endsection