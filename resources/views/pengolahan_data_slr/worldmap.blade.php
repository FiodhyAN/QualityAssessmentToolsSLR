@extends('layouts.main')
@section('container')
<div class="container">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

    <!-- menggunakan CDN untuk fetch -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fetch/3.6.2/fetch.min.js"></script>

    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-PD5eLkBx8QI5lKvS21cmPZdGhZzyI1WYKGp4d/EzXcJx0o0puW/i3qdrQ2syBw0V7RPNtWbeYV7hcSKXHJc7xg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <h1 class="text-center mb-5">{{$type}}</h1>
    <form class="form-body row g-3" action="/proses-worldmap" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <label for="project" class="form-label">Project:</label>
            <select class="form-select @error('project') is-invalid @enderror" name="project" id="project">
                <option value="" disabled selected>-- Select Project --</option>
                @foreach ($projects as $project)
                <option value="{{ $project->id }}" @if(old('project') == $project->id) selected @endif>{{ $project->project_name }}</option>
                @endforeach
            </select>
            @error('project')
                <div class="invalid-feedback">The Project field is required.</div>
            @enderror
        </div>
    </div>
    <button type="submit" class="btn btn-primary" id="submit-button">Submit</button>
</form>
    <div class="row mt-5 text-center" style="display:none" id="loading">
        <a data-fancybox="gallery" href="https://upload.wikimedia.org/wikipedia/commons/c/c7/Loading_2.gif?20170503175831">
            <img class="img-fluid" src="https://upload.wikimedia.org/wikipedia/commons/c/c7/Loading_2.gif?20170503175831" alt="Gambar 1" />
    </div>
    @if(session('worldmap'))
        <div class="row mt-5" id="data-show">
            <div class="col-md-12">
                <div class="card mt-5">
                    <div class="card-body">
                        <div id="world-map" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@section('script')
    <script>
        $("#submit-button").click(function() {
            $("#loading").show();
            $("#data-show").hide();
        });
        
        $(function() {
            $.ajax({
                url: '/getMapData',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var world_map = [];
                    var session_worldmap = {!! json_encode(session('worldmap')) !!};
                    for (var i = 0; i < session_worldmap.length; i++) {
                        world_map.push(session_worldmap[i]);
                    }
                    console.log(world_map);
                    let mapData = {};
                    let usedColors = {};
                    // initialize Fuse with country data
                    let fuse = new Fuse(data, {
                        keys: ['name'],
                        threshold: 0.3
                    });
                    // id, name, rank, nation
                    for (let i = 0; i < world_map.length; i++) {
                        let nation = world_map[i][0];
                        let color_nation = world_map[i][1];
                        // find the corresponding country code from the API data
                        let results = "None"
                        if (nation === "None") {
                            results = "None";
                        }
                        else{
                            results = fuse.search(nation)[0]?.item?.name;
                        }
                        let countryCode = data.find(c => c.name === results)?.alpha2Code;
                        if (countryCode) {
                            // add an entry to mapData with a random color, but not #87CEEB
                            let color;
                            color = color_nation;
                            usedColors[color] = true;
                            mapData[countryCode] = color;
                            $("#my-table tbody tr").each(function() {
                            if ($(this).find("td:eq(2)").text() === nation) {
                               /* $(this).find("td:eq(2)").css({"color": color, "font-weight": "bold"});*/
                            }
                            });
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
