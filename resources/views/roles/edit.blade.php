@extends('layouts.app')
@section('title')
    Edit Role
@endsection
@section('page_css')
    <link rel="stylesheet" href="https://rawgit.com/fronteed/iCheck/1.x/skins/all.css">
@endsection


@section('content')
    <ol class="breadcrumb">
          <li class="breadcrumb-item">
             <a href="{!! route('roles.index') !!}">Roles</a>
          </li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
    <div class="container-fluid">
         <div class="animated fadeIn">
             @include('coreui-templates::common.errors')
             <div class="row">
                 <div class="col-lg-12">
                      <div class="card">
                          <div class="card-header">
                              <strong>Edit Roles</strong>
                          </div>
                          <div class="card-body">
                              {!! Form::model($roles, ['route' => ['roles.update', $roles->id], 'method' => 'patch']) !!}

                              @include('roles.edit_fields')

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