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

    <h1 class="text-center mb-5">{{$type}} Relationships and Popularity</h1>
    <form class="form-body row g-3" action="/proses-metadata/{{$url}}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4">
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
        <div class="col-md-4">
            <label for="top-author" class="form-label">Top {{$type}}:</label>
            <select class="form-select @error('top-author') is-invalid @enderror" aria-label="Default select example" id="top-author" name="top-author">
                <option value="" disabled selected>-- Select Top {{$type}} --</option>
                <option value="5" @if(old('top-author') == '5') selected @endif>5</option>
                <option value="10" @if(old('top-author') == '10') selected @endif>10</option>
                <option value="20" @if(old('top-author') == '20') selected @endif>20</option>
            </select>
            @error('top-author')
                <div class="invalid-feedback">The Top {{$type}} field is required.</div>
            @enderror
        </div>
        <div class="col-md-4">
            <label for="outer-author" class="form-label">{{$type}} Display:</label>
            <select class="form-select @error('outer-author') is-invalid @enderror" aria-label="Default select example" id="outer-author" name="outer-author">
                <option value="" disabled selected>-- Select {{$type}} Display --</option>
                <option value="1" @if(old('outer-author') == '1') selected @endif>All {{$type}}</option>
                <option value="0" @if(old('outer-author') == '0') selected @endif>Relation only</option>
            </select>
            @error('outer-author')
                <div class="invalid-feedback">The {{$type}} Display field is required.</div>
            @enderror
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

    
    <div class="row mt-5">
        <div class="col-md-6" style="display:{{$display}}">
            <div class="container mb-5">
                <!-- HTML -->
                <figure>
                    <a data-fancybox="gallery" href="{{$src}}">
                        <img class="img-fluid" src="{{$src}}" alt="Gambar 1" id="my-image" />
                    </a>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <!-- make small circle with purple color -->
                            <div class="d-flex align-items-center mx-3">
                                <svg height="1em" width="1em" viewBox="0 0 512 512">
                                    <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512z" fill="purple" />
                                </svg>
                                <figcaption class="ml-2" style="color: purple;">Top Cited Authors</figcaption>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mx-3">
                            <svg height="1em" width="1em" viewBox="0 0 512 512">
                                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512z" fill="blue" />
                            </svg>
                            <figcaption class="ml-2" style="color: blue;">Cited Author</figcaption>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mx-3">
                            <svg height="1em" width="1em" viewBox="0 0 512 512">
                                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512z" fill="red" />
                            </svg>
                            <figcaption class="ml-2" style="color: red;">Uncited Author</figcaption>
                            </div>
                        </div>
                        <!-- Tambahkan badge dengan ikon -->
                        <span class="badge badge-pill badge-danger"><i class="fas fa-circle"></i></span>
                    </div>
                </figure>
                <div class="btn btn-primary mt-5" onclick="download_image()" style="display:{{$display}}" id="download-1">Download</div>
            </div>
        </div>
        <div class="col-md-6" style="display:{{$display}}">
            <table class="table" id="my-table">
                <thead>
                    <tr class="">
                        <th>Index</th>
                        <th>{{$type}}</th>
                        <th>Rank</th>
                        @if($type == "Author")
                        <th>Nation</th>
                        @else
                        <th>Title</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 20 && $i < count($author_ranks); $i++) <tr>
                        <th scope="row">{{ $author_ranks[$i][0] }}</th>
                        <td>{{ $author_ranks[$i][1] }}</td>
                        <td>{{ $author_ranks[$i][2] }}</td>
                        <td>{{ $author_ranks[$i][3] }}</td>
                        </tr>
                        @endfor
                </tbody>
            </table>
            <div class="btn btn-primary mt-5" onclick="exportToExcel()" style="display:{{$display}}"  id="download-2">Download</div>
        </div>
        @if($type == "Author")
        <div class="col-md-12" style="display: {{ $display }}">
            <div class="card mt-5">
                <div class="card-body">
                    <div id="world-map" style="height: 400px;"></div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    var myImage = document.getElementById('my-image');
    myImage.onerror = function() {
        myImage.onerror = null;
        myImage.src = 'https://upload.wikimedia.org/wikipedia/commons/c/c7/Loading_2.gif?20170503175831';
    }
    function download_image() {
        fetch(
                '{{$src}}')
            .then(response => response.blob())
            .then(blob => {
                var url = window.URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'term graph {{$type}}.png';
                document.body.appendChild(a);
                a.click();
                setTimeout(function() {
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                }, 0);
            });
    }

    function exportToExcel() {
        var table = document.getElementById("my-table");
        var html = table.outerHTML;
        var csv = [];

        // Mendapatkan baris header
        var headerRow = table.rows[0];
        var headerCells = headerRow.cells;
        var headerLength = headerCells.length;
        var rowDataHeader = [];
        for (var i = 0; i < headerLength; i++) {
            var cell = headerCells[i];
            var text = cell.textContent.replace(/\u200B/g, ""); // menghapus karakter khusus
            rowDataHeader.push('"' + text + '"');
        }
        csv.push(rowDataHeader.join(","));

        // Mendapatkan setiap baris dan sel pada tabel
        var rows = table.rows;
        var rowsLength = rows.length;
        for (var i = 1; i < rowsLength; i++) {
            var cells = rows[i].cells;
            var cellsLength = cells.length;
            var rowData = [];

            // Mengambil isi tiap sel pada baris
            for (var j = 0; j < cellsLength; j++) {
                var cell = cells[j];
                var text = cell.textContent.replace(/\u200B/g, ""); // menghapus karakter khusus
                rowData.push('"' + text + '"');
            }

            // Menggabungkan isi setiap sel dalam satu baris
            csv.push(rowData.join(","));
        }

        // Menggabungkan data menjadi satu string CSV
        var csvString = csv.join("\n");

        // Membuat tautan untuk mengunduh file
        var a = document.createElement("a");
        a.href = "data:text/csv;charset=utf-8," + encodeURIComponent(csvString);
        var totalauthor=rows.length-1;
        a.download = "top-"+totalauthor+"-rank-author.csv";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

</script>
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
                    var author_ranks = {!! json_encode($author_ranks) !!};
                    let mapData = {};
                    let usedColors = {};
                    // initialize Fuse with country data
                    let fuse = new Fuse(data, {
                        keys: ['name'],
                        threshold: 0.3
                    });
                    // id, name, rank, nation
                    for (let i = 0; i < author_ranks.length; i++) {
                        let id = author_ranks[i][0];
                        let name = author_ranks[i][1];
                        let rank = author_ranks[i][2];
                        let nation = author_ranks[i][3];
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
                            do {
                                color = getRandomColor();
                            } while (color === '#87CEEB' || usedColors[color]);
                            usedColors[color] = true;
                            mapData[countryCode] = color;
                            $("#my-table tbody tr").each(function() {
                            if ($(this).find("td:eq(2)").text() === nation) {
                                $(this).find("td:eq(2)").css("color", color);
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
