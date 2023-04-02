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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $article)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $article->id }} - {{ $article->no }}</td>
                                <td>{{ $article->title }}</td>
                                <td>
                                    @if ($article->article_user->count() == 0)
                                        <span class="badge alert-danger">Not Assign</span>
                                    @else    
                                        @foreach ($article->article_user as $user)
                                            <span style="white-space: normal"
                                                class="badge alert-primary">{{ $user->user->name }}</span>
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
    $('#article_status_table').DataTable();
    $('.select_status').select2({
        width: '15%',
        // turn off the searching
        placeholder: 'Choose Status',
        minimumResultsForSearch: Infinity,
    });

    $('.select-status').on('change', function(){

    })
</script>
@endsection