@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">User Details</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Users</a></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-primary">Back</a>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama:</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="no_ktp">No. KTP:</label>
                        <input type="text" name="no_ktp" class="form-control" value="{{ $user->no_ktp }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" name="alamat" class="form-control" value="{{ $user->alamat }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="telepon">Telepon:</label>
                        <input type="text" name="telepon" class="form-control" value="{{ $user->telepon }}" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="jenkel">Jenis Kelamin:</label>
                        <input type="text" name="jenkel" class="form-control" value="{{ $user->jenkel }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="tgl_lahir">Tanggal Lahir:</label>
                        <input type="text" name="tgl_lahir" class="form-control" value="{{ $user->tgl_lahir }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="tmpt_lahir">Tempat Lahir:</label>
                        <input type="text" name="tmpt_lahir" class="form-control" value="{{ $user->tmpt_lahir }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select name="role" class="form-control" readonly>
                            <option value="{{ $user->roles->first()->id }}">{{ $user->roles->first()->name }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection