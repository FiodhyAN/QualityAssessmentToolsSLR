@extends('layouts.main')

@section('container')
    <h1>Project Summary</h1>
    <hr>

    <div class="mb-4">
        <select name="project_id" id="project" class="col-md-4">
            @foreach ($projects as $project)
                <option value=""></option>
                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
            @endforeach
        </select>
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
            $('#project').select2({
                placeholder: 'Choose Project',
            });
        });

        // Bar Chart Question
        var options = {
            series: [],
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
            title: {
                text: 'Bar Chart'
            },
        };

        var bar_question_chart = new ApexCharts(document.querySelector("#bar_chart_question"), options);
        bar_question_chart.render();

        // Column Chart Question
        var options = {
            series: [],
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
            title: {
                text: 'Column Chart'
            },
        };

        var column_question_chart = new ApexCharts(document.querySelector("#column_chart_question"), options);
        column_question_chart.render();


        // Bar Chart User
        var options = {
            series: [],
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
            title: {
                text: 'Bar Chart'
            }
        };

        var bar_user_chart = new ApexCharts(document.querySelector("#bar_chart_user"), options);
        bar_user_chart.render();

        // Column Chart User
        var options = {
            series: [],
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
            title: {
                text: 'Column Chart'
            },
        };

        var column_user_chart = new ApexCharts(document.querySelector("#column_chart_user"), options);
        column_user_chart.render();

        $('#project').on('change', function() {
            var project_id = $(this).val();
            console.log(project_id);

            $.ajax({
                url: '{{ route('find.projectSummary') }}',
                type: 'GET',
                data: {
                    project_id: project_id
                },
                success: function(data) {
                    var question_name = data.question_name;
                    var pos_answer_question = data.pos_answer_question;
                    var net_answer_question = data.net_answer_question;
                    var neg_answer_question = data.neg_answer_question;

                    var user_name = data.user_name;
                    var pos_answer_user = data.pos_answer_user;
                    var net_answer_user = data.net_answer_user;
                    var neg_answer_user = data.neg_answer_user;

                    // Bar Chart Question
                    bar_question_chart.updateSeries([{
                        name: 'Positive',
                        data: pos_answer_question
                    }, {
                        name: 'Neutral',
                        data: net_answer_question
                    }, {
                        name: 'Negative',
                        data: neg_answer_question
                    }]);
                    bar_question_chart.updateOptions({
                        stroke: {
                            width: 1,
                            colors: ['#fff']
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
                    })

                    // Column Chart Question
                    column_question_chart.updateSeries([{
                        name: 'Positive',
                        data: pos_answer_question
                    }, {
                        name: 'Neutral',
                        data: net_answer_question
                    }, {
                        name: 'Negative',
                        data: neg_answer_question
                    }]);
                    column_question_chart.updateOptions({
                        dataLabels: {
                            enabled: false
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
                    })

                    // Bar Chart User
                    bar_user_chart.updateSeries([{
                        name: 'Positive',
                        data: pos_answer_user
                    }, {
                        name: 'Neutral',
                        data: net_answer_user
                    }, {
                        name: 'Negative',
                        data: neg_answer_user
                    }]);
                    bar_user_chart.updateOptions({
                        stroke: {
                            width: 1,
                            colors: ['#fff']
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
                    });

                    // Column Chart User
                    column_user_chart.updateSeries([{
                        name: 'Positive',
                        data: pos_answer_user
                    }, {
                        name: 'Neutral',
                        data: net_answer_user
                    }, {
                        name: 'Negative',
                        data: neg_answer_user
                    }]);
                    column_user_chart.updateOptions({
                        dataLabels: {
                            enabled: false
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
                    })
                }
            })
        })
    </script>
@endsection
