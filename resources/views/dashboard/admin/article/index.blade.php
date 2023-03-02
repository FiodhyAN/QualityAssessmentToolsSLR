@extends('layouts.main')

@section('container')
    <h1>Article Management</h1>
    <hr/>

    <div class="card">
        <div class="col mb-3 mt-3 ms-3">
            <button type="button" class="btn btn-sm btn-success px-5"><ion-icon name="add-circle-outline"></ion-icon>Add Article</button>
            <button type="button" class="btn btn-sm btn-secondary px-5"><ion-icon name="document-outline"></ion-icon>Excel Template</button>
            <button type="button" class="btn btn-sm btn-primary px-5"><ion-icon name="cloud-upload-outline"></ion-icon>Import Excel</button>

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
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>Testing</td>
                            <td>Testing</td>
                            <td>Testing</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary px-5"><ion-icon name="eye-outline"></ion-icon>View</button>
                                <button type="button" class="btn btn-sm btn-success px-5"><ion-icon name="checkmark-circle-outline"></ion-icon>Assess</button>
                            </td>
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
                url: '{!! URL::to('articleTable') !!}',
                type: 'GET',
            },
            columns: [
                {
                    title: 'ID - No',
                    data: 'id',
                    name: 'id',
                    width: '10%',
                },
                {
                    title: 'Title',
                    data: 'title',
                    name: 'title',
                    width: '30%',
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
                },
                {
                    title: 'Authors',
                    data: 'authors',
                    name: 'authors',
                    width: '20%',
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
        });

        var table = $('#assessment_table').DataTable();
    </script>
@endsection