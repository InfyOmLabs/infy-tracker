@extends('layouts.app')
@section('title')
    Report
@endsection
@section('page_css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.24/daterangepicker.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="page-header">
                <h3>Reports</h3>
                <div class="filter-container">
                    <div class="mr-2">
                        <label for="projects" class="lbl-block"><b>Assign To</b></label>
                        {!!Form::select('drp_user',$users,Auth::id(),['id'=>'filterUser','class'=>'form-control','style'=>'min-width:150px;hight:35', 'placeholder' => 'All'])  !!}
                    </div>
                    <div class="mr-2">
                        <label for="project" class="lbl-block"><b>Project</b></label>
                        {!!Form::select('drp_project',$projects,null,['id'=>'filterProject','class'=>'form-control','style'=>'min-width:150px;hight:35', 'placeholder' => 'All'])  !!}
                    </div>
                    <div class="mr-2">
                        <label for="activity" class="lbl-block"><b>Activity</b></label>
                        {!!Form::select('drp_activity',$activityTypes,null,['id'=>'filterActivity','class'=>'form-control','style'=>'min-width:150px;hight:35', 'placeholder' => 'All'])  !!}
                    </div>
                    <div class="mr-2">
                        <label for="task" class="lbl-block"><b>Tasks</b></label>
                        {!!Form::select('drp_activity',$tasks,null,['id'=>'filterTask','class'=>'form-control','style'=>'min-width:150px;hight:35', 'placeholder' => 'All'])  !!}
                    </div>
                    <div id="filterDate" class="filter-date">
                        <i class="far fa-calendar-alt" aria-hidden="true"></i>&nbsp;
                        <span></span> <b class="caret"></b>
                    </div>
                    {!! Form::hidden('start_date',null,['id'=>'startDate']) !!}
                    {!! Form::hidden('end_date',null,['id'=>'endDate']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('reports.table')
                            <div class="pull-right mr-3">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.24/daterangepicker.js"></script>
@endsection

@section('scripts')
    <script>
        let reportUrl = '{{ route('reports.index') }}';
    </script>
    <script src="{{ mix('assets/js/report/report.js') }}"></script>
@endsection

