@extends('layouts.app')
@section('title')
    Create Report
@endsection
@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="page-header">
                <h3 class="page__heading">New Report</h3>
                <div class="filter-container">
                    <a class="btn btn-secondary ml-1" href="{{url()->previous()}}">Back</a>
                </div>
            </div>
            <div class="row">
                @include('coreui-templates::common.errors')
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            {!! Form::open(['route' => 'reports.store']) !!}

                            @include('reports.fields')

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        let reportUrl = "{{ url('reports') }}";
        let usersOfProjects = "{{ url('users-of-projects') }}";
        let projectsOfClient = "{{ url('projects-of-client') }}";
    </script>
    <script src="{{ mix('assets/js/report/report.js') }}"></script>
@endsection
