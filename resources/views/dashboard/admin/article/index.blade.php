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
                    <div class="table-responsive">
                        <table id="score_table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Question</th>
                                    <th>User</th>
                                    <th>Score</th>
                                    <th>Sum</th>
                                </tr>
                            </thead>
                            <tbody id="scoreData">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                },
                {
                    title: 'Title',
                    data: 'title',
                    name: 'title',
                    render: function(data, type, row) {
                        return '<span style="white-space:normal">' + data + "</span>";
                    }
                },
                {
                    title: 'Year',
                    data: 'year',
                    name: 'year',
                },
                {
                    title: 'Publication',
                    data: 'publication',
                    name: 'publication',
                    render: function(data, type, row) {
                        return '<span style="white-space:normal">' + data + "</span>";
                    }
                },
                {
                    title: 'Authors',
                    data: 'authors',
                    name: 'authors',
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
                                assessment_table.ajax.reload();
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

        table.on('click', '.scoreArticle', function() {
            // var button = $(event.relatedTarget)
            var title = $(this).data('title');
            $('.modal-title-score').text('Score For ' + title)

            // Score Data
            var article_id = $(this).data('id');
            // console.log(id);
            $.ajax({
                url: '{{ route('article.score') }}',
                type: 'GET',
                data: {
                    article_id: article_id
                },
                dataType: 'JSON',
                success: function(result){
                    console.log(result);
                    var html = '';
                    var no = 1;

                    $.each(result, function(key, value){
                        var sum = 0;
                        var name = '';
                        var score = '';
                        // console.log(value.article_user_questionaire[0].article_user.user.name);
                        $.each(value.article_user_questionaire, function(key, value){
                            if(value.article_user == null) {
                                name += '';
                                score += '';
                                sum += 0;
                            }
                            else {
                                sum += value.score;
                                name += value.article_user.user.name + '<br>';
                                score += value.score + '<br>';
                            }
                        });
                        // sum += value.article_user_questionaire.score;
                        html += '<tr>';
                        html += '<td>' + no++ + '</td>';
                        html += '<td>' + value.question + '</td>';
                        html += '<td class="name">' + name + '</td>';
                        html += '<td class="text-center">' + score + '</td>';
                        html += '<td class="text-center">' + sum + '</td>';
                        html += '</tr>';
                    });
                    $('#scoreData').html(html);
                },
                error: function(error){
                    console.log(error);
                }
            })
        });

        var assessment_table = $('#assessment_table').DataTable({
            //no column sorting and searching false
            serverSide: true,
            processing: true,
            ajax: {
                url: '{{ route('assignment.table', $project->project->id) }}',
                type: 'GET',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'id_no',
                    name: 'id_no',
                },
                {
                    data: 'title',
                    name: 'title',
                },
                {
                    data: 'assessed',
                    name: 'assessed',
                    class: 'text-center',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                }
            ],
            rowCallback: function(row, data, index) {
                if (data.id_no == false) {
                    $(row).find('td:eq(2)').attr('colspan', '3');
                    $(row).find('td:eq(2)').text('No Article Assigned');
                    $(row).find('td:eq(3)').addClass('d-none');
                    $(row).find('td:eq(4)').addClass('d-none');
                    $(row).find('td:eq(2)').addClass('text-center');
                }
            }
        });
    </script>
@endsection
