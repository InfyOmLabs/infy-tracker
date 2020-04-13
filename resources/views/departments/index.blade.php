@extends('layouts.app')
@section('title')
    Departments
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="page-header">
                <h3>Departments</h3>
                <div>
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#AddModal"></i>New Department</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('departments.table')
                        </div>
                    </div>
                    @include('departments.modal')
                    @include('departments.edit_modal')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let departmentCreateUrl = "{{ route('departments.store') }}";
        let departmentUrl = "{{url('departments')}}/";
    </script>
    <script src="{{ mix('assets/js/department/department.js') }}"></script>
@endsection
