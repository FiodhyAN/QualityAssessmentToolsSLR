@extends('layouts.main')

@section('container')
    <h1>Article Management</h1>
    <hr />

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <a href="/dashboard/admin/project"><button type="button" class="btn btn-secondary mb-2">
            <ion-icon name="arrow-back"></ion-icon> Back to Project
        </button></a>


    <div class="card">
        <div class="col mb-3 mt-3 ms-3">
            <a href="/dashboard/admin/article/create?id={{ $project->project->id }}"><button type="button"
                    class="btn btn-sm btn-success px-5 mb-2">
                    <ion-icon name="add-circle-outline"></ion-icon>Add Article
                </button></a>
            <a href="/article/download"><button type="button" class="btn btn-sm btn-secondary px-5 mb-2">
                    <ion-icon name="document-outline"></ion-icon>Excel Template
                </button></a>
            <button type="button" class="btn btn-sm btn-primary px-5 mb-2" id="import_excel" data-bs-toggle="modal"
                data-bs-target="#exampleModal">
                <ion-icon name="cloud-upload-outline"></ion-icon>Import Excel
            </button>
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
                                @if (count($user->article_user) == 0 || $user->article_user[0]->article == null)
                                    <td colspan="3" class="text-center">No Article Assigned</td>
                                    <td class="d-none"></td>
                                    <td class="d-none"></td>
                                @else
                                    <td>
                                        @foreach ($user->article_user as $article_user)
                                            {{ $article_user->article->id }} - {{ $article_user->article->no }} <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($user->article_user as $article_user)
                                            {{ $article_user->article->title }} <br>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        @foreach ($user->article_user as $article_user)
                                            @if ($article_user->is_assessed == 1)
                                                <span class="badge bg-success">Assessed</span> <br>
                                            @else
                                                <span class="badge bg-danger">Not Assessed</span> <br>
                                            @endif
                                        @endforeach
                                    </td>
                                @endif
                                <td>
                                    <a
                                        href="/dashboard/admin/assign?pid={{ $project->project->id }}&uid={{ $user->id }}"><button
                                            type="button" class="btn btn-sm btn-success px-5"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-user-check">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="8.5" cy="7" r="4"></circle>
                                                <polyline points="17 11 19 13 23 9"></polyline>
                                            </svg> Assign</button></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal for score --}}
    <div class="modal fade" id="modalScore" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title-score" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal For Import -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_import_excel" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="project_id" value="{{ $project->project->id }}">
                            <label for="formFile" class="form-label">Import Excel</label>
                            <input class="form-control" type="file" id="formFile" name="excel_file">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        // get data title from scoreArticle button
        $('#modalScore').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var title = button.data('title')
            var modal = $(this)
            modal.find('.modal-title-score').text('Score For ' + title)
        });


        $('#form_import_excel').on('submit', function(e) {
            e.preventDefault();
            console.log('test');
            $('#exampleModal').modal('hide');

            // show loading sweeralert
            Swal.fire({
                title: 'Importing...',
                html: 'Please wait while importing article.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
            });

            // post with ajax
            $.ajax({
                url: '{{ route('article.import') }}',
                type: 'POST',
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function(result) {
                    console.log(result);
                    // close loading sweeralert
                    Swal.close();
                    Swal.fire({
                        title: 'Success!',
                        text: 'Article has been imported.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(isConfirmed => {
                        table.ajax.reload();
                        //reset form
                        $('#form_import_excel')[0].reset();
                    })
                },
                error: function(result) {
                    console.log(result);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Article failed to import.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                }
            });
        });
        // Datatable
        var table = $('#article_table').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route('article.table', $project->project->id) }}',
                type: 'GET',
            },
            columns: [{
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
                    title: 'Publication',
                    data: 'publication',
                    name: 'publication',
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
                                location.reload();
                            })
                        },
                        error: function(result) {
                            console.log(result);
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
            columnDefs: [{
                    "orderable": false,
                    "targets": 0
                },
                {
                    "searchable": false,
                    "targets": 0
                },
                {
                    "width": 20,
                    "targets": 0
                }
            ],
        });
    </script>
@endsection
