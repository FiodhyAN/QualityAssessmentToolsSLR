@extends('layouts.main')

@section('container')
    <h1>Project Summary</h1>
    <hr>

    <a href="/dashboard/summary"><button type="button" class="btn btn-secondary mb-4">
            <ion-icon name="arrow-back"></ion-icon> Back to Project
        </button></a>

    {{-- <div class="card">
        <div class="card-body">
            <div class="card-title">
                <h5>Article Not Assessed</h5>
            </div>
            <div class="table-responsive">
                <table id="article_not_assessed" class="table table-striped table-bordered" style="width:100%">
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
                                    @foreach ($article->article_user as $user)
                                        <span style="white-space: normal" class="badge alert-primary">{{ $user->user->name }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div> --}}

    <h6 class="mb-0 text-uppercase">Score Per Question</h6>
    <hr />
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div id="bar_chart_question"></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div id="column_chart_question"></div>
                </div>
            </div>
        </div>
    </div>

    <h6 class="mb-0 text-uppercase">Score Per Reviewer</h6>
    <hr />
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div id="bar_chart_user"></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div id="column_chart_user"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#article_not_assessed').DataTable();
        });

        var question_name = {!! json_encode($question_name) !!};
        var pos_answer_question = {!! json_encode($pos_answer_question) !!};
        var net_answer_question = {!! json_encode($net_answer_question) !!};
        var neg_answer_question = {!! json_encode($neg_answer_question) !!};

        var user_name = {!! json_encode($user_name) !!};
        var pos_answer_user = {!! json_encode($pos_answer_user) !!};
        var net_answer_user = {!! json_encode($net_answer_user) !!};
        var neg_answer_user = {!! json_encode($neg_answer_user) !!};

        // question name to array
        // Bar Chart Question
        var options = {
            series: [{
                name: 'Positive',
                data: pos_answer_question
            }, {
                name: 'Neutral',
                data: net_answer_question
            }, {
                name: 'Negative',
                data: neg_answer_question
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                stackType: '100%'
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    dataLabels: {
                        total: {
                            enabled: true,
                            offsetX: 0,
                            style: {
                                fontSize: '13px',
                                fontWeight: 900
                            }
                        }
                    }
                },
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            title: {
                text: 'Bar Chart'
            },
            xaxis: {
                categories: question_name,
                labels: {
                    formatter: function(val) {
                        return val
                    }
                }
            },
            yaxis: {
                title: {
                    text: undefined
                },
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val
                    }
                },
                marker: {
                    fillColors: ['#008FFB', '#00E396', '#FF0000']
                }
            },
            fill: {
                opacity: 1,
                colors: ['#008FFB', '#00E396', '#FF0000']
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40,
                markers: {
                    fillColors: ['#008FFB', '#00E396', '#FF0000']
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#bar_chart_question"), options);
        chart.render();

        // Column Chart Question
        var options = {
            series: [{
                name: 'Positive',
                data: pos_answer_question
            }, {
                name: 'Neutral',
                data: net_answer_question
            }, {
                name: 'Negative',
                data: neg_answer_question
            }],
            chart: {
                type: 'bar',
                height: 350,
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            title: {
                text: 'Column Chart'
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: question_name,
            },
            yaxis: {
                title: {
                    text: undefined
                }
            },
            fill: {
                opacity: 1,
                colors: ['#008FFB', '#00E396', '#FF0000']
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val
                    }
                },
                marker: {
                    fillColors: ['#008FFB', '#00E396', '#FF0000']
                }
            },
            legend: {
                markers: {
                    fillColors: ['#008FFB', '#00E396', '#FF0000']
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#column_chart_question"), options);
        chart.render();


        // Bar Chart User
        var options = {
            series: [{
                name: 'Positive',
                data: pos_answer_user
            }, {
                name: 'Neutral',
                data: net_answer_user
            }, {
                name: 'Negative',
                data: neg_answer_user
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                stackType: '100%'
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    dataLabels: {
                        total: {
                            enabled: true,
                            offsetX: 0,
                            style: {
                                fontSize: '13px',
                                fontWeight: 900
                            }
                        }
                    }
                },
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            title: {
                text: 'Bar Chart'
            },
            xaxis: {
                categories: user_name,
                labels: {
                    formatter: function(val) {
                        return val
                    }
                }
            },
            yaxis: {
                title: {
                    text: undefined
                },
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val
                    }
                },
                marker: {
                    fillColors: ['#008FFB', '#00E396', '#FF0000']
                }
            },
            fill: {
                opacity: 1,
                colors: ['#008FFB', '#00E396', '#FF0000']
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40,
                markers: {
                    fillColors: ['#008FFB', '#00E396', '#FF0000']
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#bar_chart_user"), options);
        chart.render();

        // Column Chart User
        var options = {
            series: [{
                name: 'Positive',
                data: pos_answer_user
            }, {
                name: 'Neutral',
                data: net_answer_user
            }, {
                name: 'Negative',
                data: neg_answer_user
            }],
            chart: {
                type: 'bar',
                height: 350,
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            title: {
                text: 'Column Chart'
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: user_name,
            },
            yaxis: {
                title: {
                    text: undefined
                }
            },
            fill: {
                opacity: 1,
                colors: ['#008FFB', '#00E396', '#FF0000']
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val
                    }
                },
                marker: {
                    fillColors: ['#008FFB', '#00E396', '#FF0000']
                }
            },
            legend: {
                markers: {
                    fillColors: ['#008FFB', '#00E396', '#FF0000']
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#column_chart_user"), options);
        chart.render();
    </script>
@endsection
