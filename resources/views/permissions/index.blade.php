@extends('layouts.app')
@section('title')
    Permissions
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
             @include('flash::message')
            <div class="page-header">
                <h3>Permissions</h3>
                <div style="display: flex;align-items: center">
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#AddModal"></i>New Permission</a>
                </div>
            </div>
             <div class="row">
                 <div class="col-lg-12">
                     <div class="card">
                         <div class="card-body">
                             @include('permissions.table')
                              <div class="pull-right mr-3">
                                     
                              </div>
                         </div>
                         @include('permissions.modal')
                         @include('permissions.edit_modal')
                     </div>
                  </div>
             </div>
         </div>
    </div>
@endsection

@section('scripts')
    <script>
       let permissionCreateUrl='{{route('permissions.store')}}';
       let permissionUrl='{{url('permissions')}}/';
    </script>
    <script src="{{ mix('assets/js/permissions/permission.js') }}"></script>
@endsection

