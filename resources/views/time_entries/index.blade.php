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
                        <div class="mr-2" style="margin-top: 20px;">
                            <div class="btn-group" role="group">
                                <button id="timeEntriesActions" type="button" class="btn btn-primary dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <div class="dropdown-menu" aria-labelledby="timeEntriesActions"
                                     x-placement="bottom-start"
                                     style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <a href="#" class="dropdown-item filter-container__btn" id="new_entry"
                                       data-toggle="modal"
                                       data-target="#timeEntryAddModal">New Time Entry</a>
                                    <a href="javascript:void(0)" class="dropdown-item filter-container__btn"
                                       id="copyTodayEntry">Copy Today Activity</a>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-danger" style="display: none" id="tmValidationErrorsBox"></div>
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
        let canManageEntries = "{{ (Auth::user()->can('manage_time_entries')) ? true : false }}";
        let copyTodayActivity = "{{ url('copy-today-activity') }}/";
    </script>
    <script src="{{ mix('assets/js/time_entries/time_entry.js') }}"></script>
@endsection
