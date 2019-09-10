@extends('layouts.app')
@section('title')
    Users
@endsection
@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="page-header">
                <h3 class="page__heading">Users</h3>
                <div style="display: flex;align-items: center">
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#AddModal"></i>New
                        User</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('users.table')
                            <div class="pull-right mr-3">

                            </div>
                        </div>
                        @include('users.modal')
                        @include('users.edit_modal')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let createUserUrl = "{{ route('users.store') }}";
    </script>
    <script src="{{ mix('assets/js/users/user.js') }}"></script>
@endsection

