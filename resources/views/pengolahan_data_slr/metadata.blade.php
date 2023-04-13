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

    <h1 class="text-center mb-5">{{$type}} Relationships and Popularity</h1>
    <form class="form-body row g-3" action="/proses-metadata/{{$url}}" method="POST" onsubmit="return validateForm()">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <select class="form-select" name="project" id="project">
                    <option></option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" aria-label="Default select example" id="top-author" name="top-author">
                    <option></option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" aria-label="Default select example" id="outer-author" name="outer-author">
                    <option></option>
                    <option value="1">All {{$type}}</option>
                    <option value="0">Relation only</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <div class="row mt-5">
        <div class="col-md-6" style="display:{{$display}}">
            <div class="container text-center mb-5">
                <!-- HTML -->
                <a data-fancybox="gallery" href="{{$src}}">
                    <img class="img-fluid" src="{{$src}}" alt="Gambar 1" id="my-image" />
                </a>
                <div class="btn btn-primary mt-5" onclick="download_image()" style="display:{{$display}}" id="download-1">Download</div>
            </div>
        </div>
        <div class="col-md-6" style="display:{{$display}}">
            <table class="table" id="my-table">
                <thead>
                    <tr class="text-center">
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
    </div>
</div>

<script>
    $(document).ready(function(){
        var type = {!! json_encode($type) !!}
        $('#project').select2({
            placeholder: 'Select Project',
        });
        $('#top-author').select2({
            placeholder: 'Select Top ' + type,
            minimumResultsForSearch: -1
        });
        $('#outer-author').select2({
            placeholder: 'Select ' + type + ' Display',
            minimumResultsForSearch: -1
        });
    })
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

    function validateForm() {
        var project = document.getElementById("project").value;
        var top_author = document.getElementById("top-author").value;
        var outer_author = document.getElementById("outer-author").value;
        if (project == "empty-field" || top_author == "empty-field" || outer_author == "empty-field") {
            alert("Please select an option in all fields.");
            return false;
        }
    }

</script>
@endsection