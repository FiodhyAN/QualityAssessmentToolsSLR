@extends('layouts.main')

@section('container')
    <div class="row">
        <div class="card">
            <div class="card-body" id="testing">
                <div class="row">
                    <div class="col-6" id="test">
                        <label for="" class="form-label">From</label>
                        <input type="text" id="yearFrom" class="form-control form-control-sm filter" name="yearFrom"
                            autocomplete="off" placeholder="Select Year From">
                    </div>
                    <div class="col-6">
                        <label for="" class="form-label">To</label>
                        <input type="text" id="yearTo" class="form-control form-control-sm filter" name="yearTo"
                            autocomplete="off" placeholder="Select Year To" disabled>
                    </div>
                </div>
                <div id="chart"></div>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('script')
    <script>
        $("#yearFrom").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoClose: true,
            container: '#testing',
        }).on('changeDate', function(e) {
            $('#yearTo').val('');
            $('#yearTo').datepicker('setStartDate', e.date);
            $('#yearTo').prop('disabled', false);
        });
        $("#yearTo").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoClose: true,
            container: '#testing',
        });

        $('.filter').on('change', function() {
            var yearFrom = $('#yearFrom').val();
            var yearTo = $('#yearTo').val();
            console.log(yearFrom, yearTo);
            if (yearFrom != '' && yearTo != '') {
                $.ajax({
                    url: '{{ route('find.articleType') }}',
                    type: 'GET',
                    data: {
                        yearFrom: yearFrom,
                        yearTo: yearTo
                    },
                    success: function(response) {
                        var journal = [];
                        var proceeding = [];
                        response.articles.forEach(element => {
                            if (element.type == 'Journal') {
                                journal.push(element.total);
                            }
                            if (element.type == 'Proceeding') {
                                proceeding.push(element.total);
                            }
                        });
                        console.log(journal, proceeding);
                        chart.updateOptions({
                            series : [{
                                name: 'Journal',
                                data: journal
                            }, {
                                name: 'Proceeding',
                                data: proceeding
                            }],
                            xaxis: {
                                categories: response.year,
                            },
                        })
                    }
                });
            }
        });

        var options = {
            series: [{
                name: 'Journal',
                data: []
            }, {
                name: 'Proceeding',
                data: []
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
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: [],
            },
            fill: {
                opacity: 1
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
@endsection
