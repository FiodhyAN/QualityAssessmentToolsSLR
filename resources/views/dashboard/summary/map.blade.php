@extends('layouts.main')

@section('container')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div id="world-map" style="height: 400px"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function getRandomColor(excludeColor) {
            var color;
            do {
                color = '#' + Math.floor(Math.random() * 16777215).toString(16);
            } while (color === excludeColor);
            return color;
        }
        $(function() {
            $.ajax({
                url: '/getMapData',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var countryArray = ['Italy', 'France', 'Spain', 'United States of America', 'Argentina',
                        'Indonesia'
                    ];
                    let mapData = {};
                    let usedColors = {};
                    for (let country of countryArray) {
                        // find the corresponding country code from the API data
                        let countryCode = data.find(c => c.name === country)?.alpha2Code;
                        if (countryCode) {
                            // add an entry to mapData with a random color, but not #87CEEB
                            let color;
                            do {
                                color = getRandomColor();
                            } while (color === '#87CEEB' || usedColors[color]);
                            usedColors[color] = true;
                            mapData[countryCode] = color;
                        }
                    }
                    console.log(mapData);
                    var map = new jvm.Map({
                        map: 'world_mill',
                        backgroundColor: '#87CEEB',
                        container: $('#world-map'),
                        series: {
                            regions: [{
                                values: mapData,
                                attribute: 'fill'
                            }]
                        }
                    });
                }
            })
        });
    </script>
@endsection
