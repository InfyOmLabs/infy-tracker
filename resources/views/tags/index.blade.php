@extends('layouts.app')
@section('title')
    Tags
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
             @include('flash::message')
            <div class="page-header">
                <h3 class="page__heading">Tags</h3>
                <div style="display: flex;align-items: center">
                    <a href="#" class="btn btn-primary" onclick="setBulkTags()" data-toggle="modal"
                       data-target="#AddModal"></i>Bulk Tags</a>
                    &nbsp;
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#AddModal"></i>New Tag</a>
                </div>
            </div>
             <div class="row">
                 <div class="col-lg-12">
                     <div class="card">
                         <div class="card-body">
                             @include('tags.table')
                              <div class="pull-right mr-3">
                                     
                              </div>
                         </div>
                         @include('tags.modal')
                         @include('tags.edit_modal')
                     </div>
                  </div>
             </div>
         </div>
    </div>
@endsection

@section('scripts')
    <script>
       let tagCreateUrl='{{route('tags.store')}}';
       let tagUrl='{{url('tags')}}/';
    </script>
    <script src="{{ mix('assets/js/tags/tag.js') }}"></script>
@endsection

