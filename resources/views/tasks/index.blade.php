@extends('layouts.app')
@section('title')
    Tasks
@endsection
@section('page_css')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="https://rawgit.com/fronteed/iCheck/1.x/skins/all.css">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="page-header">
                <h3 class="page__heading">Tasks</h3>
                <div class="filter-container">
                    <div class="mr-2">
                        <label class="lbl-block"><b>Assign To</b></label>
                        {!!Form::select('drp_users',$assignees,Auth::id(),['id'=>'filter_user','class'=>'form-control','style'=>'min-width:150px;', 'placeholder' => 'All'])  !!}
                    </div>
                    <div class="mr-2">
                        <label class="lbl-block"><b>Project</b></label>
                        {!!Form::select('drp_project',$projects,null,['id'=>'filter_project','class'=>'form-control','style'=>'min-width:150px;', 'placeholder' => 'All'])  !!}
                    </div>
                    <div class="mr-2">
                        <label class="lbl-block"><b>Due Date</b></label>
                        {!! Form::text('due_date_filter', null, ['id'=>'dueDateFilter','class' => 'form-control', 'autocomplete' => 'off','style'=>'min-width:150px;']) !!}
                    </div>
                    <div class="mr-2">
                        <label class="lbl-block"><b>Status</b></label>
                        {!!Form::select('drp_status',$status,0,['id'=>'filter_status','class'=>'form-control','style'=>'min-width:150px;'])  !!}
                    </div>
                    <a href="#" class="btn btn-primary filter-container__btn" data-toggle="modal" data-target="#AddModal"></i>New
                        Task</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('tasks.table')
                            <div class="pull-right mr-3">

                            </div>
                        </div>
                        @include('tasks.modal')
                        @include('tasks.edit_modal')
                        @include('tasks.task_details')
                        @include('time_entries.modal')
                        @include('time_entries.edit_modal')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.11.4/standard/ckeditor.js"></script>
@endsection

@section('scripts')
    <script>
        let taskIndexUrl = '{{route('tasks.index')}}';
        let taskUrl = '{{url('tasks')}}/';
        let taskDetailUrl = '{{url('task-details')}}';
        let createTaskUrl = '{{route('tasks.store')}}';
        let timeEntryUrl = "{{url('time-entries')}}/";
        let getTaskUrl = "{{url('get-tasks')}}/";
        let projectsURL = "{{url('projects')}}/";
        let taskStatusJson = '{!! json_encode($taskStatus) !!}';
        let taskStatus = $.parseJSON(taskStatusJson)
        let taskBadgesJson = '{!! json_encode($taskBadges) !!}';
        let taskBadges = $.parseJSON(taskBadgesJson);
        let taskDetailActionColumnIsVisible = true;
        let reportStartDate = '';
        let reportEndDate = '';
    </script>
    <script src="{{ mix('assets/js/task/task.js') }}"></script>
    <script src="{{ mix('assets/js/task/task_time_entry.js') }}"></script>
    <script src="{{ mix('assets/js/time_entries/time_entry.js') }}"></script>
@endsection

