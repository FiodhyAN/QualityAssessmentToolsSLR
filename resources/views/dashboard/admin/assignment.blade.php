@extends('layouts.main')
@section('container')
    <h1>Assessment Management</h1>
    <hr>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="assessment_table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>ID - No</th>
                            <th>Article</th>
                            <th>Assessed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                @if (count($user->article_user) == 0)
                                    <td colspan="3" class="text-center">No Article Assigned</td>
                                @else
                                    <td>
                                        @foreach ($user->article_user as $article_user)
                                            {{ $article_user->article->no }} <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($user->article_user as $article_user)
                                            {{ $article_user->article->title }} <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($user->article_user as $article_user)
                                            @if ($article_user->is_assessed == 1)
                                                <ion-icon name="checkmark-circle-outline"></ion-icon> <br>
                                            @else
                                                <ion-icon name="close-circle-outline"></ion-icon> <br>
                                            @endif
                                        @endforeach
                                    </td>
                                @endif
                                <td>
                                    <button type="button" class="btn btn-sm btn-success px-5"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-check"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg> Assign</button>
                                </td>
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
        // Datatable
        var assessment_table = $('#assessment_table').DataTable({
            //no column sorting and searching false
            columnDefs: [
                { "orderable": false, "targets": 0 },
                { "searchable": false, "targets": 0 },
                { "width": 20, "targets": 0 }
            ],
        });
    </script>
@endsection