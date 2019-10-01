@extends('layouts.app')
@section('title')
    Edit Report
@endsection
@section('page_css')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('coreui-templates::common.errors')
            <div class="page-header">
                <h3 class="page__heading">Edit Report</h3>
                <div class="filter-container">
                    <a class="btn btn-secondary ml-1" href="{{url()->previous()}}">Back</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            {!! Form::model($report, ['route' => ['reports.update', $report->id], 'method' => 'put']) !!}

                            @include('reports.fields')

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
@endsection
@section('scripts')
    <script>
        let reportUrl = "{{ url('reports') }}";
        let usersOfProjects = "{{ url('users-of-projects') }}";
        let projectsOfClient = "{{ url('projects-of-client') }}";
    </script>
    <script src="{{ mix('assets/js/report/report.js') }}"></script>
@endsection
