@extends('layouts.main')
@section('container')
    
    <div class="modal fade" id="exampleVerticallycenteredModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Add New User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/addUser" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="col mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Enter Name..." value="{{ old('name') }}" autocomplete="off" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" placeholder="Enter Username..." autocomplete="off" value="{{ old('username') }}" required>
                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Password (Default: Password = Username)</label>
                        <input type="text" class="form-control" name="password" placeholder="Enter Password..." autocomplete="off" value="{{ old('password') }}">
                    </div>      
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
        </div>
    </div>
    
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Edit User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/updateUser" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="col mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Enter Name..." value="{{ old('name') }}" autocomplete="off" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" placeholder="Enter Username..." autocomplete="off" value="{{ old('username') }}" required>
                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Password (Isi jika ingin diubah)</label>
                        <input type="text" class="form-control" name="password" placeholder="Enter Password..." autocomplete="off" value="{{ old('password') }}">
                    </div>
                    <input type="hidden" name="user_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <h1>User Management</h1>
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
                  <button type="button" class="btn btn-sm btn-success px-5" data-bs-toggle="modal" data-bs-target="#exampleVerticallycenteredModal"><ion-icon name="add-circle-outline"></ion-icon>Add User</button>
              </div>
              <div class="card-body">
                  <div class="table-responsive">
                      <table id="example" class="table table-striped table-bordered" style="width:100%">
                          <thead>
                              <tr>
                                  <th>No</th>
                                  <th>Name</th>
                                  <th>Username</th>
                                  <th>Action</th>
                              </tr>
                          </thead>
                          <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>
                                        <a class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-username="{{ $user->username }}">
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
        $('#modalEdit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var name = button.data('name') // Extract info from data-* attributes
            var username = button.data('username') // Extract info from data-* attributes
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('.modal-body input[name="name"]').val(name)
            modal.find('.modal-body input[name="username"]').val(username)
            modal.find('.modal-body input[name="user_id"]').val(id)
        });
    </script>
@endsection