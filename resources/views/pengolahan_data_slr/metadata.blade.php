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

    <h1 class="text-center mb-5">{{$type}} relationships and popularity</h1>
    <form class="form-body row g-3" action="/proses-metadata/{{$type}}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <select class="form-select" name="project">
                    <option disabled selected>-- Select Project --</option>
                    <option value="1">1</option>
                    @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" aria-label="Default select example" id="top-author" name="top-author">
                    <option disabled selected>-- Select Top {{$type}} --</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" aria-label="Default select example" id="outer-author" name="outer-author">
                    <option disabled selected>-- Select {{$type}} display --</option>
                    <option value="1">All {{$type}}</option>
                    <option value="0">Relation only</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <div class="row">
        <div class="col-md-6">
            <h1 class="text-center mt-5">{{$type}} graph</h1>
            <div class="container text-center">
                <!-- HTML -->
                <a data-fancybox="gallery" href="{{$src}}">
                    <img class="img-fluid" src="{{$src}}" alt="Gambar 1" id="my-image" />
                </a>

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
                    for (var i = 0; i < headerLength; i++) {
                        var cell = headerCells[i];
                        var text = cell.textContent.replace(/\u200B/g, ""); // menghapus karakter khusus
                        csv.push('"' + text + '"');
                    }

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
                    a.download = "my-table.csv";
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }
                </script>
                <div class="btn btn-primary mt-5" onclick="download_image()" style="display:{{$display}}" id="download-1">Download</div>
            </div>
        </div>
        <div class="col-md-6">
            <h1 class="text-center mt-5">{{$type}} rank table</h1>
            <table class="table" id="my-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>{{$type}}</th>
                        <th>Rank</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 20 && $i < count($author_ranks); $i++) <tr>
                        <th scope="row">{{ $author_ranks[$i][0] }}</th>
                        <td>{{ $author_ranks[$i][1] }}</td>
                        <td>{{ $author_ranks[$i][2] }}</td>
                        </tr>
                        @endfor
                </tbody>
            </table>
            <div class="btn btn-primary mt-5" onclick="exportToExcel()" style="display:{{$display}}"  id="download-2">Download</div>
        </div>
    </div>
</div>
@endsection