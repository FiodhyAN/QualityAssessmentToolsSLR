@extends('layouts.main')

@section('container')
    <h1>Article Management</h1>
    <hr/>

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="col mb-3 mt-3 ms-3">
            <a href="/dashboard/admin/article/create?id={{ $project->project->id }}"><button type="button" class="btn btn-sm btn-success px-5 mb-2"><ion-icon name="add-circle-outline"></ion-icon>Add Article</button></a>
            <button type="button" class="btn btn-sm btn-secondary px-5 mb-2"><ion-icon name="document-outline"></ion-icon>Excel Template</button>
            <button type="button" class="btn btn-sm btn-primary px-5 mb-2"><ion-icon name="cloud-upload-outline"></ion-icon>Import Excel</button>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="article_table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

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
        var table = $('#article_table').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route('article.table') }}',
                type: 'GET',
            },
            columns: [
                {
                    title: 'ID - No',
                    data: 'no',
                    name: 'no',
                    width: '10%',
                },
                {
                    title: 'Title',
                    data: 'title',
                    name: 'title',
                    width: '30%',
                    render: function(data, type, row) {
                        return '<span style="white-space:normal">' + data + "</span>";
                    }
                },
                {
                    title: 'Year',
                    data: 'year',
                    name: 'year',
                    width: '10%',
                },
                {
                    title: 'Publisher',
                    data: 'publisher',
                    name: 'publisher',
                    width: '10%',
                    render: function(data, type, row) {
                        return '<span style="white-space:normal">' + data + "</span>";
                    }
                },
                {
                    title: 'Authors',
                    data: 'authors',
                    name: 'authors',
                    width: '20%',
                    render: function(data, type, row) {
                        return '<span style="white-space:normal">' + data + "</span>";
                    }
                },
                {
                    title: 'Action',
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    width: '20%',
                },
            ]
        }).on('click', '.deleteArticle', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: 'Delete Article',
                text: 'Are you sure you want to delete this article?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/deleteArticle',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Article has been deleted.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(isConfirmed => {
                                table.ajax.reload();
                            })
                        },
                        error: function(result) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Article failed to delete.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            })
                        }
                    });
                }
            })
        });

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