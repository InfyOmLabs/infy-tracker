@extends('layouts.app')
@section('title')
    Report Details
@endsection
@section('page_css')
    <link href="{{mix('assets/style/css/report.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('coreui-templates::common.errors')
            @include('flash::message')
            <div class="page-header">
                <h3 class="page__heading">View Report</h3>
                <div class="filter-container">
                    <a href="{{ route('reports.edit', $report->id) }}"
                       class="btn btn-primary filter-container__btn mr-1">
                        Edit
                    </a>
                    <button class="btn btn-danger delete-btn" data-id="{{$report->id}}">Delete</button>
                    <a class="btn btn-secondary ml-1" href="{{url()->previous()}}">Back</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    @include('reports.report_format')
                    @include('tasks.task_details')
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $( document ).ready(function() {
            $(".reports__client-row-title").click(function(){
                $(this).find("i.fa-caret-up").toggleClass("fa-rotate");
                $(this).parent().parent().find('.collapse-row').slideToggle();
            });
            $(".reports__project-header").click(function(){
                $(this).find("i.fa-caret-up").toggleClass("fa-rotate");
                $(this).parent().next('.reports__project-container').slideToggle();
            });
            $(".reports__developer-header").click(function(){
                $(this).find("i.fa-caret-up").toggleClass("fa-rotate");
                $(this).parent().next('.reports__task-container').slideToggle();
            });
        });
        let reportUrl = "{{ url('reports') }}/";
        let taskUrl = '{{url('tasks')}}/';
        let taskDetailUrl = '{{url('task-details')}}';
        let taskDetailActionColumnIsVisible = false;
        let reportStartDate = '{{$report->start_date->startOfDay()}}';
        let reportEndDate = '{{$report->end_date->endOfDay()}}';
        let usersOfProjects = "{{ url('users-of-projects') }}";
        let projectsOfClient = "{{ url('projects-of-client') }}";
    </script>
    <script src="{{ mix('assets/js/report/report.js') }}"></script>
    <script src="{{ mix('assets/js/task/task_time_entry.js') }}"></script>
@endsection
