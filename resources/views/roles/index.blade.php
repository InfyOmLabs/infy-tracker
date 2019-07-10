@extends('layouts.app')
@section('title')
    Roles
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
             @include('flash::message')
            <div class="page-header">
                <h3>Roles</h3>
                <div style="display: flex;align-items: center">
                    <a class="pull-right btn btn-primary" href="{!! route('roles.create') !!}">New Role</a>
                </div>
            </div>
             <div class="row">
                 <div class="col-lg-12">
                     <div class="card">
                         <div class="card-body">
                             @include('roles.table')
                              <div class="pull-right mr-3">
                                     
                              </div>
                         </div>
                     </div>
                  </div>
             </div>
         </div>
    </div>
@endsection
@section('scripts')
    <script>
        let roleUrl = "{{url('roles')}}/";
    </script>
    <script src="{{ mix('assets/js/roles/role.js') }}"></script>
@endsection

