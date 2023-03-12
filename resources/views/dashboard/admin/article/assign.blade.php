@extends('layouts.main')

@section('container')
    <h1>Assign Article</h1>
    <hr>
    <a href="/dashboard/admin/project/{{ $project_id }}"><button type="button" class="btn btn-secondary mb-2"><ion-icon name="arrow-back"></ion-icon> Back to Project</button></a>
    <div class="card">
        <div class="col mb-3 mt-3 ms-3">
            <button disabled type="button" class="btn btn-primary" id="assign_btn"><ion-icon name="bookmark"></ion-icon> Assign Article</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="notAssignTable" class="table table-striped table-bordered" style="width:100%">
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
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>


    <h1>Article Management</h1>
    <hr>
    <div class="card">
        <div class="col mb-3 mt-3 ms-3">
            <button disabled type="button" class="btn btn-danger" id="delete_btn"><ion-icon name="trash"></ion-icon> Delete Article</button>
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
        var rows_selected = [];
        var rows_selected2 = [];

        function updateDataTableSelectAllCtrl(table){
            var $table             = table.table().node();
            var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
            var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
            var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

            // If none of the checkboxes are checked
            if($chkbox_checked.length === 0){
                chkbox_select_all.checked = false;
                if('indeterminate' in chkbox_select_all){
                    chkbox_select_all.indeterminate = false;
                }

            // If all of the checkboxes are checked
            } else if ($chkbox_checked.length === $chkbox_all.length){
                chkbox_select_all.checked = true;
                if('indeterminate' in chkbox_select_all){
                    chkbox_select_all.indeterminate = false;
                }

            // If some of the checkboxes are checked
            } else {
                chkbox_select_all.checked = true;
                if('indeterminate' in chkbox_select_all){
                    chkbox_select_all.indeterminate = true;
                }
            }
        }

        $(document).ready(function() {
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
                        title: '<input type="checkbox" id="head_cb" name="select_all" value="1">',
                        data: 'id',
                        name: 'id',
                        width: '5%',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="cb_child" value="' + data + '">';
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
                ],
                rowCallback: function(row, data, dataIndex) {
                    var rowId = data['id'];
                    console.log(rowId);
                    if ($.inArray(rowId, rows_selected) !== -1) {
                        $(row).find('input[type="checkbox"]').prop('checked', true);
                        $(row).addClass('selected');
                    }
                },
            });

            $('#notAssignTable tbody').on('click', '.cb_child', function(e){
                var $row = $(this).closest('tr');

                // Get row data
                var data = articleNotAssign.row($row).data();

                // Get row ID
                var rowId = data['id'];

                // Determine whether row ID is in the list of selected row IDs
                var index = $.inArray(rowId, rows_selected);

                // If checkbox is checked and row ID is not in list of selected row IDs
                if(this.checked && index === -1){
                    rows_selected.push(rowId);
                    $('#assign_btn').prop('disabled', false);
                // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
                } else if (!this.checked && index !== -1){
                    rows_selected.splice(index, 1);
                    if(rows_selected.length == 0){
                        $('#assign_btn').prop('disabled', true);
                    }
                }

                if(this.checked){
                    $row.addClass('selected');
                } else {
                    $row.removeClass('selected');
                }

                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(articleNotAssign);

                // Prevent click event from propagating to parent
                e.stopPropagation();
            });

            // Handle click on table cells with checkboxes
            $('#notAssignTable').on('click', 'tbody td, thead th:first-child', function(e){
                $(this).parent().find('input[type="checkbox"]').trigger('click');
            });

            $('thead input[name="select_all"]', articleNotAssign.table().container()).on('click', function(e){
                if(this.checked){
                    $('#notAssignTable tbody input[type="checkbox"]:not(:checked)').trigger('click');
                } else {
                    $('#notAssignTable tbody input[type="checkbox"]:checked').trigger('click');
                }

                // Prevent click event from propagating to parent
                e.stopPropagation();
            });

            articleNotAssign.on('draw', function(){
                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(articleNotAssign);
            });
        });


        // Assign Table
        function updateDataTableSelectAllCtrlAssign(table){
            var $table             = table.table().node();
            var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
            var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
            var chkbox_select_all  = $('thead input[name="select_all_assign"]', $table).get(0);

            // If none of the checkboxes are checked
            if($chkbox_checked.length === 0){
                chkbox_select_all.checked = false;
                if('indeterminate' in chkbox_select_all){
                    chkbox_select_all.indeterminate = false;
                }

            // If all of the checkboxes are checked
            } else if ($chkbox_checked.length === $chkbox_all.length){
                chkbox_select_all.checked = true;
                if('indeterminate' in chkbox_select_all){
                    chkbox_select_all.indeterminate = false;
                }

            // If some of the checkboxes are checked
            } else {
                chkbox_select_all.checked = true;
                if('indeterminate' in chkbox_select_all){
                    chkbox_select_all.indeterminate = true;
                }
            }
        }

        $(document).ready(function(){
            var articleAssign = $('#AssignTable').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
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
                        title: '<input type="checkbox" id="head_cb_assign" name="select_all_assign" value="1">',
                        data: 'id',
                        name: 'id',
                        width: '5%',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="cb_child_assign" value="' + data + '">';
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
                ],
                rowCallback: function(row, data, dataIndex) {
                    var rowId = data['id'];
                    console.log(rowId);
                    if ($.inArray(rowId, rows_selected2) !== -1) {
                        $(row).find('input[type="checkbox"]').prop('checked', true);
                        $(row).addClass('selected');
                    }
                },
            });

            $('#AssignTable tbody').on('click', '.cb_child_assign', function(e){
                var $row = $(this).closest('tr');

                // Get row data
                var data = articleAssign.row($row).data();

                // Get row ID
                var rowId = data['id'];

                // Determine whether row ID is in the list of selected row IDs
                var index = $.inArray(rowId, rows_selected2);

                // If checkbox is checked and row ID is not in list of selected row IDs
                if(this.checked && index === -1){
                    rows_selected2.push(rowId);
                    $('#delete_btn').prop('disabled', false);
                // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
                } else if (!this.checked && index !== -1){
                    rows_selected2.splice(index, 1);
                    if(rows_selected2.length == 0){
                        $('#delete_btn').prop('disabled', true);
                    }
                }

                if(this.checked){
                    $row.addClass('selected');
                } else {
                    $row.removeClass('selected');
                }

                // Update state of "Select all" control
                updateDataTableSelectAllCtrlAssign(articleAssign);

                // Prevent click event from propagating to parent
                e.stopPropagation();
            });

            // Handle click on table cells with checkboxes
            $('#AssignTable').on('click', 'tbody td, thead th:first-child', function(e){
                $(this).parent().find('input[type="checkbox"]').trigger('click');
            });

            $('thead input[name="select_all_assign"]', articleAssign.table().container()).on('click', function(e){
                if(this.checked){
                    $('#AssignTable tbody input[type="checkbox"]:not(:checked)').trigger('click');
                } else {
                    $('#AssignTable tbody input[type="checkbox"]:checked').trigger('click');
                }

                // Prevent click event from propagating to parent
                e.stopPropagation();
            });

            articleNotAssign.on('draw', function(){
                // Update state of "Select all" control
                updateDataTableSelectAllCtrlAssign(articleAssign);
            });
        });
        // console.log(articleNotAssign);
    </script>
@endsection
