@extends('layouts.main')

@section('container')
    <h1>Project Summary</h1>
    <hr>

    <a href="/dashboard/admin/summary"><button type="button" class="btn btn-secondary mb-2">
            <ion-icon name="arrow-back"></ion-icon> Back to Project
        </button></a>

    <div class="card">
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
                                <td class="text-center">
                                    @foreach ($article->article_user as $user)
                                        <span class="badge alert-primary">{{ $user->user->name }}</span><br>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#article_not_assessed').DataTable();
        });

        // Bar Chart Question
        var options = {
            series: [{
                name: 'Positive',
                data: [4, 5, 4, 10, 4]
            }, {
                name: 'Neutral',
                data: [5, 3, 3, 3, 4]
            }, {
                name: 'Negative',
                data: [2, 2, 9, 3, 1]
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
                categories: ['QA1', 'QA2', 'QA3', 'QA4', 'QA5'],
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
                }
            },
            fill: {
                opacity: 1,
                colors: ['#008FFB', '#00E396', '#FF0000']
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40
            }
        };

        var chart = new ApexCharts(document.querySelector("#bar_chart_question"), options);
        chart.render();

        // Column Chart Question
        var options = {
            series: [{
                name: 'Net Profit',
                data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
            }, {
                name: 'Revenue',
                data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
            }, {
                name: 'Free Cash Flow',
                data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
            }],
            chart: {
                type: 'bar',
                height: 350
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
                categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            },
            yaxis: {
                title: {
                    text: '$ (thousands)'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "$ " + val + " thousands"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#column_chart_question"), options);
        chart.render();
    </script>
@endsection
