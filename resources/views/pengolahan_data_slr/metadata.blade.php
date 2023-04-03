@extends('layouts.main')
@section('container')
<div class="container">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

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
                document.getElementById('my-image').onerror = function() {
                    this.onerror = null;
                    this.src = 'https://upload.wikimedia.org/wikipedia/commons/c/c7/Loading_2.gif?20170503175831';
                };
                </script>

            </div>
        </div>
        <div class="col-md-6">
            <h1 class="text-center mt-5">{{$type}} rank table</h1>
            <table class="table">
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
        </div>
    </div>
</div>
@endsection