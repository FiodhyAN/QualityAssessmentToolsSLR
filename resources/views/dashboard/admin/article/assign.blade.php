@extends('layouts.main')

@section('container')
    <h1>Assign Article</h1>
    <hr>
    <a href="/dashboard/admin/project/{{ $project_id }}"><button type="button" class="btn btn-secondary mb-2 ms-3"><ion-icon name="arrow-back"></ion-icon> Back to Project</button></a>
    <div class="card">
        <div class="col mb-3 mt-3 ms-3">
            <button disabled type="button" class="btn btn-primary" id="assign_btn"><ion-icon name="bookmark"></ion-icon> Assign Article</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="notAssignTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="head_cb"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>


    <h1>Article Management</h1>
    <hr>
    <div class="card">
        <div class="col mb-3 mt-3 ms-3">
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="AssignTable" class="table table-striped table-bordered" style="width:100%">
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
@endsection
@section('script')
    <script>
        var project_id = {{ $project_id }};
        var user_id = {{ $user_id }};

        $(document).ready(function() {
            var rows_selected = [];
            var articleNotAssign = $('#notAssignTable').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: {
                    url: '{{ route('notAssigned.table') }}',
                    data: {
                        project_id: project_id,
                        user_id: user_id
                    },
                    type: 'GET',
                },
                columns: [
                    {
                        targets: 0,
                        sortable: false,
                        render: function(data, type, full, meta) {
                            return '<input type="checkbox" class="cb_child">';
                        }
                    },
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
                        title: 'Publication',
                        data: 'publication',
                        name: 'publication',
                        width: '25%',
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
                ]
            });
        });

        $('#head_cb').on('click', function(e) {
            if ($(this).is(':checked', true)) {
                $(".cb_child").prop('checked', true);
                $('#assign_btn').prop('disabled', false);
            } else {
                $(".cb_child").prop('checked', false);
                $('#assign_btn').prop('disabled', true);
            }

        });
        $('#notAssignTable tbody').on('click', '.cb_child', function(e) {
            if ($('.cb_child:checked').length > 0) {
                $('#assign_btn').prop('disabled', false);
            } else {
                $('#assign_btn').prop('disabled', true);
            }
            if ($('.cb_child:checked').length == $('.cb_child').length) {
                $('#head_cb').prop('checked', true);
            } else {
                $('#head_cb').prop('checked', false);
            }
        });

        var articleAssign = $('#AssignTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('assigned.table') }}',
                data: {
                    project_id: project_id,
                    user_id: user_id
                },
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
                    width: '3%',
                },
            ]
        });
        // console.log(articleNotAssign);
    </script>
@endsection
