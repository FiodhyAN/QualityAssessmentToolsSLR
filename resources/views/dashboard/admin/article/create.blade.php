@extends('layouts.main')

@section('container')
    <h1>Add New Article</h1>
    <hr>

    <div class="card">
        <div class="card-header">
          <h6 class="mb-0">New Article</h6>
        </div>
        <div class="card-body">
            <form action="/dashboard/admin/article/store" class="row g-3" method="POST">
                @csrf
                <div class="col-6">
                    <label for="kode_artikel" class="form-label">Kode Artikel</label>
                    <small class="text-danger">*required</small>
                    <input class="form-control @error('kode_artikel') is-invalid @enderror" type="text" placeholder="Kode" name="kode_artikel" aria-label="default input example" value="{{ old('kode_artikel') }}">
                    @error('kode_artikel')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="file" class="form-label">Upload File</label>
                    <small class="text-danger">*pdf only</small>
                    <input class="form-control @error('file') is-invalid @enderror" type="file" id="formFile" name="file" value="{{ old('file') }}">
                    @error('file')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="title" class="form-label">Title</label>
                    <small class="text-danger">*required</small>
                    <input class="form-control @error('title') is-invalid @enderror" type="text" placeholder="Judul Artikel" name="title" aria-label="default input example" value="{{ old('title') }}">
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="index" class="form-label">Index Journal</label>
                    <input class="form-control" type="text" placeholder="Index" name="index" aria-label="default input example" value="{{ old('index') }}">
                </div>
                <div class="col-6">
                    <label for="quartile" class="form-label">Quartile</label>                
                    <input class="form-control" type="text" placeholder="If Scopus" name="quartile" aria-label="default input example" value="{{ old('quartile') }}">
                </div>
                <div class="col-6">
                    <label for="year" class="form-label">Year</label>
                    <small class="text-danger">*required</small>
                    <input class="form-control @error('year') is-invalid @enderror" type="text" placeholder="YYYY" name="year" aria-label="default input example" id="year" value="{{ old('year') }}">
                    @error('year')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="authors" class="form-label">Authors</label>
                    <small class="text-danger">*required</small>
                    <input class="form-control @error('authors') is-invalid @enderror" type="text" placeholder="Authors" name="authors" aria-label="default input example" value="{{ old('authors') }}">
                    @error('authors')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="abstract" class="form-label">Abstract</label>                
                    <textarea rows="10" name="abstract" class="form-control" placeholder="Abstract" spellcheck="false" data-ms-editor="true">{{ old('abstract') }}</textarea>
                </div>
                <div class="col-12">
                    <label for="keywords" class="form-label">Keywords</label>                
                    <textarea rows="5" name="keywords" class="form-control" placeholder="Keyword" spellcheck="false" data-ms-editor="true">{{ old('keywords') }}</textarea>
                </div>
                <div class="col-6">
                    <label for="language" class="form-label">Language of Article</label>
                    <small class="text-danger">*required</small>
                    <input class="form-control @error('language') is-invalid @enderror" type="text" placeholder="Language" name="language" aria-label="default input example" value="{{ old('language') }}">
                    @error('language')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="article_type" class="form-label">Type of Article</label>
                    <small class="text-danger">*required</small>
                    <select class="form-select @error('article_type') is-invalid @enderror" name="article_type" aria-label="Default select example">
                        <option disabled selected>Select Type</option>
                        <option value="journal" {{ old('article_type') == 'journal' ? 'selected' : '' }}>Journal</option>
                        <option value="proceeding" {{ old('article_type') == 'proceeding' ? 'selected' : '' }}>Proceeding</option>
                    </select>
                    @error('article_type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="publisher" class="form-label">Publisher</label>
                    <small class="text-danger">*required</small>
                    <input class="form-control @error('publisher') is-invalid @enderror" type="text" placeholder="Publisher" name="publisher" aria-label="default input example" value="{{ old('publisher') }}">
                    @error('publisher')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="references_ori" class="form-label">References Original</label>                
                    <textarea rows="10" name="references_ori" class="form-control" placeholder="References Original" spellcheck="false" data-ms-editor="true">{{ old('references_ori') }}</textarea>
                </div>
                <div class="col-12">
                    <label for="references_filter" class="form-label">References Filter</label>                
                    <textarea rows="5" name="references_filter" class="form-control" placeholder="References Filter" spellcheck="false" data-ms-editor="true">{{ old('references_filter') }}</textarea>
                </div>
                <div class="col-4">
                    <label for="cited" class="form-label">Cited</label>
                    <small class="text-danger">*required</small>
                    <input class="form-control @error('cited') is-invalid @enderror" type="text" placeholder="Cited" name="cited" aria-label="default input example" value="{{ old('cited') }}">
                    @error('cited')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-4">
                    <label for="cited_gs" class="form-label">Cited Google Scholar</label>
                    <small class="text-danger">*required</small>
                    <input class="form-control @error('cited_gs') is-invalid @enderror" type="text" placeholder="Cited" name="cited_gs" aria-label="default input example" value="{{ old('cited_gs') }}">
                    @error('cited_gs')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-4">
                    <label for="cited_other" class="form-label">Citing Other Article</label>
                    <small class="text-danger">*required</small>
                    <input class="form-control @error('cited_other') is-invalid @enderror" type="text" placeholder="Cited" name="cited_other" aria-label="default input example" value="{{ old('cited_other') }}">
                    @error('cited_other')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="keyword" class="form-label">Keyword</label>
                    <small class="text-danger">*required</small>
                    <input class="form-control @error('keyword') is-invalid @enderror" type="text" placeholder="Keyword" name="keyword" aria-label="default input example" value="{{ old('keyword') }}">
                    @error('keyword')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="edatabase" class="form-label">Edatabase</label>
                    <small class="text-danger">*required</small>
                    <input class="form-control @error('edatabase') is-invalid @enderror" type="text" placeholder="Database 1" name="edatabase" aria-label="default input example" value="{{ old('edatabase') }}">
                    @error('edatabase')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="edatabase2" class="form-label">Edatabase 2</label>                    
                    <input class="form-control" type="text" placeholder="Database 2" name="edatabase2" aria-label="default input example" value="{{ old('edatabase2') }}">
                </div>

                {{-- Make button on the bottom right corner --}}
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary"><ion-icon name="save"></ion-icon> Save Article</button>
                </div>
            </form>
        </div>
      </div>
@endsection
@section('script')
    <script>
        $(function(){
            $('#year').datepicker({
                format: "yyyy",
                viewMode: "years", 
                minViewMode: "years",
                autoclose: true,
                orientation: "auto"
            });
        })
    </script>
@endsection