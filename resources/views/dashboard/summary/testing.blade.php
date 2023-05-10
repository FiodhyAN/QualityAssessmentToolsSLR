@extends('layouts.main')

@section('container')
    <h1>Testing Chart</h1>

    <div class="row">
        <div class="card">
            <div class="card-body">
                <div id="chart"></div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var options = {
            series: [{
                name: "SAMPLE A",
                data: [
                    [16.4, 5.4],
                    [21.7, 2],
                    [25.4, 3],
                    [19, 2],
                    [10.9, 1],
                    [13.6, 3.2],
                    [10.9, 7.4],
                    [10.9, 0],
                    [10.9, 8.2],
                    [16.4, 0],
                    [16.4, 1.8],
                    [13.6, 0.3],
                    [13.6, 0],
                    [29.9, 0],
                    [27.1, 2.3],
                    [16.4, 0],
                    [13.6, 3.7],
                    [10.9, 5.2],
                    [16.4, 6.5],
                    [10.9, 0],
                    [24.5, 7.1],
                    [10.9, 0],
                    [8.1, 4.7],
                    [19, 0],
                    [21.7, 1.8],
                    [27.1, 0],
                    [24.5, 0],
                    [27.1, 0],
                    [29.9, 1.5],
                    [27.1, 0.8],
                    [22.1, 2]
                ]
            }],
            chart: {
                height: 350,
                type: 'scatter',
                zoom: {
                    enabled: true,
                    type: 'xy'
                }
            },
            xaxis: {
                tickAmount: 10,
                labels: {
                    formatter: function(val) {
                        return parseFloat(val).toFixed(1)
                    }
                }
            },
            yaxis: {
                tickAmount: 7
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
@endsection
