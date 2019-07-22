@extends('layouts.app')
@section('title')
    Create Report
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
            <div class="page-header">
                <h3>New Report</h3>
                <div class="filter-container">
                    <a class="btn btn-secondary ml-1" href="{{route('reports.index')}}">Back</a>
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
@section('page_js')
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/report/report.js') }}"></script>
@endsection
