@extends('layouts.app')
@section('title')
    Time Entries
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="page-header">
                <h3 class="page__heading">Time Entries</h3>
                <div class="filter-container">
                    @can('manage_time_entries')
                    <div class="mr-2">
                        <label for="projects" class="lbl-block"><b>User</b></label>
                        {!!Form::select('drp_user',$users,Auth::id(),['id'=>'filterUser','class'=>'form-control','style'=>'min-width:150px;hight:35', 'placeholder' => 'All'])  !!}
                    </div>
                    @endcan
                    <div class="mr-2">
                        <label class="lbl-block"><b>Project</b></label>
                        {!!Form::select('drp_project',$projects,null,['id'=>'filter_project','class'=>'form-control','style'=>'min-width:150px;', 'placeholder' => 'All'])  !!}
                    </div>
                    <div class="mr-2">
                        <label for="projects" class="lbl-block"><b>Activity Type</b></label>
                        {!!Form::select('drp_activity',$activityTypes,null,['id'=>'filterActivity','class'=>'form-control','style'=>'min-width:150px;hight:35', 'placeholder' => 'All'])  !!}
                    </div>
                    <a href="#" class="btn btn-primary filter-container__btn" id="new_entry" data-toggle="modal"
                       data-target="#timeEntryAddModal"></i>New Time Entry</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('time_entries.table')
                            <div class="pull-right mr-3">

                            </div>
                        </div>
                        @include('time_entries.modal')
                        @include('time_entries.edit_modal')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let taskUrl = '{{url('tasks')}}/';
        let timeEntryUrl = "{{url('time-entries')}}/";
        let projectsURL = "{{url('projects')}}/";
        let getTaskUrl = "{{url('get-tasks')}}/";
    </script>
    <script src="{{ mix('assets/js/time_entries/time_entry.js') }}"></script>
@endsection