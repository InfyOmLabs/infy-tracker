@extends('layouts.app')
@section('title')
   New Role
@endsection
@section('page_css')
    <link rel="stylesheet" href="https://rawgit.com/fronteed/iCheck/1.x/skins/all.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-plus-square-o fa-lg"></i>
                            <strong>New Role</strong>
                        </div>
                        <div class="card-body">
                            @include('coreui-templates::common.errors')
                            {!! Form::open(['route' => 'roles.store']) !!}

                            @include('roles.fields')

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
@endsection
@section('scripts')
    <script>
        $(function(){
            $('.permission-checkbox').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                increaseArea: '10%'
            });
        });
    </script>
@endsection
