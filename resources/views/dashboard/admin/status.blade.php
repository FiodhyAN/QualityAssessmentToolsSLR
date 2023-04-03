@extends('layouts.main')
@section('container')
    <h1>Article Status</h1>
    <hr>

    <a href="/dashboard/admin/project"><button type="button" class="btn btn-secondary mb-2">
            <ion-icon name="arrow-back"></ion-icon> Back to Project
        </button>
    </a>

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="d-flex justify-content-end div_select">
                    <select class="select_status" name="status">
                        <option disabled selected>-- Choose Status --</option>
                        <option value="all">All Article</option>
                        <option value="not_assign">Not Assign</option>
                        <option value="assessed">Assessed</option>
                        <option value="not_assessed">Not Assessed</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table id="article_status_table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID - No</th>
                            <th>Article</th>
                            <th>Users</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $article)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $article->id }} - {{ $article->no }}</td>
                                <td><span style="white-space: normal;">{{ $article->title }}</span></td>
                                <td>
                                    @if ($article->article_user->count() == 0)
                                        <span class="badge alert-danger">Not Assign</span>
                                    @else    
                                        @foreach ($article->article_user as $user)
                                            <span style="white-space: normal"
                                                class="badge alert-primary">{{ $user->user->name }}</span><br>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if ($article->article_user->count() == 0)
                                        <span class="badge alert-danger">Not Assign</span>
                                    @else
                                        @foreach ($article->article_user as $user)
                                            @if ($user->is_assessed == true)
                                                <span class="badge alert-success">Assessed</span><br>
                                            @else
                                                <span class="badge alert-warning">Not Assessed</span><br>
                                            @endif
                                        @endforeach
                                    @endif
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
    var table = $('#article_status_table').DataTable();
    $('.select_status').select2({
        width: '15%',
        // turn off the searching
        placeholder: 'Choose Status',
        minimumResultsForSearch: Infinity,
    });

    $('.select_status').on('change', function(){
        var status = $(this).val();
        var project_id = {{ request()->pid }};
        console.log(project_id);
        console.log(status);

        $.ajax({
            url: '{{ route('find.status') }}',
            method: 'GET',
            data: {
                status: status,
                project_id: project_id
            },
            success: function(data){
                table.clear().draw();
                let no = 1;
                for (let index = 0; index < data.length; index++) {
                    table.row.add([
                        no,
                        data[index].id + ' - ' + data[index].no,
                        data[index].title,
                        data[index].article_user.length == 0 ? '<span class="badge alert-danger">Not Assign</span>' : data[index].article_user.map(user => '<span style="white-space: normal" class="badge alert-primary">' + user.user.name + '</span><br>').join(''),
                        data[index].article_user.length == 0 ? '<span class="badge alert-danger">Not Assign</span>' : data[index].article_user.map(user => user.is_assessed == true ? '<span class="badge alert-success">Assessed</span><br>' : '<span class="badge alert-warning">Not Assessed</span><br>').join('')
                    ]).draw();
                }
            }
        })
    })
</script>
@endsection