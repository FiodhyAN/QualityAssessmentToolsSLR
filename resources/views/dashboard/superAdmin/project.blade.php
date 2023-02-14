@extends('layouts.main')
@section('container')

    <div class="modal fade" id="exampleVerticallycenteredModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Add New Project</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/addProject" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="col mb-3">
                        <label class="form-label">Project Name</label>
                        <input type="text" class="form-control @error('project_name') is-invalid @enderror" name="project_name" placeholder="Enter Project Name..." value="{{ old('project_name') }}" autocomplete="off" required>
                        @error('project_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Limit Max Reviewer</label>
                        <input type="number" class="form-control @error('limit') is-invalid @enderror" name="limit" placeholder="Enter Max Reviewer..." autocomplete="off" value="{{ old('limit') }}" required>
                        @error('limit')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Project Admin</label>
                        <select class="select_user" name="admin_project">
                            <option disabled selected>-- Select User --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('admin_project') == $user->id ? 'selected' : ''}}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('admin_project')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>      
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Project</button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Edit Project</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/updateProject" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="col mb-3">
                        <label class="form-label">Project Name</label>
                        <input type="text" class="form-control @error('project_name') is-invalid @enderror" name="project_name" placeholder="Enter Project Name..." value="{{ old('project_name') }}" autocomplete="off" required>
                        @error('project_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Limit Max Reviewer</label>
                        <input type="number" class="form-control @error('limit') is-invalid @enderror" name="limit" placeholder="Enter Max Reviewer..." autocomplete="off" value="{{ old('limit') }}" required>
                        @error('limit')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Project Admin</label>
                        <select class="select_edit_user" name="admin_project" disabled>
                            <option disabled selected>-- Select User --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('admin_project') == $user->id ? 'selected' : ''}}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('admin_project')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>  
                    <input type="hidden" name="project_id">    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <h1>Project Management</h1>
    <hr/>
    @if (session()->has('success'))    
        <div class="alert alert-dismissible fade show py-2 bg-success">
            <div class="d-flex align-items-center">
            <div class="fs-3 text-white"><ion-icon name="checkmark-circle-sharp"></ion-icon>
            </div>
            <div class="ms-3">
                <div class="text-white">{{ session('success') }}</div>
            </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="col mb-3 mt-3 ms-3">
            <button type="button" class="btn btn-sm btn-success px-5" data-bs-toggle="modal" data-bs-target="#exampleVerticallycenteredModal"><ion-icon name="add-circle-outline"></ion-icon>Add Project</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Admin Project</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $project->project_name }}</td>
                            <td>{{ $project->user->name }}</td>
                            <td>
                                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEdit" data-id="{{ $project->id }}" data-name="{{ $project->project_name }}" data-limit="{{ $project->limit_reviewer }}" data-user="{{ $project->user_id }}">
                                    <ion-icon name="create-outline"></ion-icon> Edit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    @if (session()->has('errors'))
        <script>
            $(document).ready(function(){
                $('#exampleVerticallycenteredModal').modal('show');
                $('#modalEdit').modal('show');
            });
        </script>
    @endif
    <script>
        // Edit Modal
        $('#modalEdit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id')
            var name = button.data('name')
            var limit = button.data('limit')
            var user = button.data('user')
            var modal = $(this)
            modal.find('.modal-body input[name="project_id"]').val(id)
            modal.find('.modal-body input[name="project_name"]').val(name)
            modal.find('.modal-body input[name="limit"]').val(limit)
            modal.find('.modal-body select[name="admin_project"]').val(user)
            $('.select_edit_user').trigger('change');
        })
    </script>
    <script>
        $(document).ready(function() {
            $('.select_user').select2({
                dropdownParent: $('#exampleVerticallycenteredModal .modal-content')
            });
            $('.select_edit_user').select2({
                dropdownParent: $('#modalEdit .modal-content')
            });
        } );
    </script>
@endsection