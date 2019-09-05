@extends('layouts.app')
@section('title')
    Projects
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="page-header">
                <h3 class="page__heading">Projects</h3>
                <div class="filter-container">
                    <div class="mr-2">
                        <label for="clients" class="lbl-block"><b>Client</b></label>
                        {!!Form::select('drp_client',$clients,null,['id'=>'filterClient','class'=>'form-control','style'=>'min-width:150px;hight:35', 'placeholder' => 'All'])  !!}
                    </div>
                    <a href="#" class="btn btn-primary filter-container__btn" data-toggle="modal" data-target="#AddModal"></i>New
                        Project</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('projects.table')
                            <div class="pull-right mr-3">

                            </div>
                        </div>
                        @include('projects.modal')
                        @include('projects.edit_modal')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let projectCreateUrl = '{{route('projects.store')}}';
        let projectUrl = '{{url('projects')}}/';
    </script>
    <script src="{{ mix('assets/js/projects/project.js') }}"></script>
@endsection

