@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/formpart.css') }}">
@endsection
@section('container')
    <h1>Assessed Article</h1>
    <hr>

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="d-flex justify-content-end div_select">
                    <select class="select_project" name="project">
                        <option disabled selected>-- Select Project --</option>
                        <option value="all">All Project</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table id="assessment_table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID - No</th>
                            <th>Title</th>
                            <th>Project Name</th>
                            <th>Year</th>
                            <th>Publication</th>
                            <th>Authors</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal for Score Button --}}
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
                                    <th>Answer</th>
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
@endsection
@section('script')
    <script>
        // Inisiasi Select2
        $(document).ready(function() {
            $('.select_project').select2({
                width: '50%',
            });
        })

        // Inisiasi DataTable
        var table = $('#assessment_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('assessed.table') }}',
            columns: [{
                    data: 'no',
                    name: 'no'
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function(data, type, row) {
                        return '<span style="white-space: normal;">' + data + '</span>"';
                    }
                },
                {
                    data: 'project_name',
                    name: 'project_name'
                },
                {
                    data: 'year',
                    name: 'year'
                },
                {
                    data: 'publication',
                    name: 'publication',
                    render: function(data, type, row) {
                        return '<span style="white-space: normal;">' + data + '</span>"';
                    }
                },
                {
                    data: 'authors',
                    name: 'authors',
                    render: function(data, type, row) {
                        return '<span style="white-space: normal;">' + data + '</span>"';
                    }
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ]
        });

        // Filter Data berdasarkan Project
        $('.select_project').on('change', function() {
            var project_id = $(this).val();

            $('#assessment_table').DataTable().destroy();
            $('#assessment_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('assessed.table') }}',
                    data: {
                        project_id: project_id
                    }
                },
                columns: [{
                        data: 'no',
                        name: 'no',
                    },
                    {
                        data: 'title',
                        name: 'title',
                        render: function(data, type, row) {
                            return '<span style="white-space: normal;">' + data + '</span>"';
                        }
                    },
                    {
                        data: 'project_name',
                        name: 'project_name'
                    },
                    {
                        data: 'year',
                        name: 'year'
                    },
                    {
                        data: 'publication',
                        name: 'publication',
                        render: function(data, type, row) {
                            return '<span style="white-space: normal;">' + data + '</span>"';
                        }
                    },
                    {
                        data: 'authors',
                        name: 'authors',
                        render: function(data, type, row) {
                            return '<span style="white-space: normal;">' + data + '</span>"';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
            });
        })

        // Score Modal
        table.on('click', '.scoreArticle', function() {
            var title = $(this).data('title');
            $('.modal-title-score').text('Score For ' + title)
            var article_id = $(this).data('id');
            $.ajax({
                url: '{{ route('reviewer.score') }}',
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
                        var pos_answer = value.pos_answer;
                        var net_answer = value.net_answer;
                        var neg_answer = value.neg_answer;
                        var answer = '';
                        // var sum = 0;
                        // var name = '';
                        // var score = '';
                        // // console.log(value.article_user_questionaire[0].article_user.user.name);
                        $.each(value.article_user_questionaire, function(key, value){
                            if (value.article_user == null) {
                                answer += '';
                            }
                            else {
                                if (value.score == 1) {
                                    answer += pos_answer;
                                }
                                else if (value.score == 0) {
                                    answer += net_answer;
                                }
                                else if (value.score == -1) {
                                    answer += neg_answer;
                                }
                            }
                        });
                        // sum += value.article_user_questionaire.score;
                        html += '<tr>';
                        html += '<td>' + no++ + '</td>';
                        html += '<td><span style="white-space: normal;">' + value.question + '</span></td>';
                        html += '<td><span style="white-space: normal;">' + answer + '</span></td>';
                        html += '</tr>';
                    });
                    $('#scoreData').html(html);
                },
                error: function(error){
                    console.log(error);
                }
            })
        });
    </script>
@endsection