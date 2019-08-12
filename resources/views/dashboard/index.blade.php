@extends('layouts.app')
@section('title')
    Dashboard
@endsection

@section('page_css')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.24/daterangepicker.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.css">
    <link rel="stylesheet" href="{{ mix('assets/style/css/dashboard.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="page-header">
                <h3>Dashboard</h3>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <h5>Custom Report</h5>
                                </div>
                                <div class="col-4">
                                    <div class="row">
                                        @permission('manage_users')
                                        <div class="col-5">
                                            {!! Form::select('users', $users, Auth::id(), ['id' => 'userId','class'=>'user_filter_dropdown']) !!}
                                        </div>
                                        @endpermission
                                        <div class="col-7">
                                            <div id="time_range" class="time_range">
                                                <i class="far fa-calendar-alt"
                                                   aria-hidden="true"></i>&nbsp;&nbsp;<span></span> <b
                                                    class="caret"></b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="work-report-container" class="pt-2">
                                <canvas id="daily-work-report"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="page-header">
                                <h5>Daily Work Report</h5>
                                <div id="rightData">
                                    <div id="developers-report-date-picker" class="time_range">
                                        <i class="far fa-calendar-alt" aria-hidden="true"></i>&nbsp;&nbsp;
                                        <span></span> <b class="caret"></b>
                                    </div>
                                </div>
                            </div>
                            <div id="developers-daily-work-report-container" class="pt-2">
                                <canvas id="developers-daily-work-report"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.24/daterangepicker.js"></script>
@endsection

@section('scripts')
    <script>
        let userReportUrl = "{{route('users-work-report')}}";
        let userDeveloperReportUrl = "{{route('developers-work-report')}}";
    </script>
    <script src="{{ mix('assets/js/dashboard/dashboard.js') }}"></script>
    <script src="{{ mix('assets/js/dashboard/developers-daily-report.js') }}"></script>
@endsection
