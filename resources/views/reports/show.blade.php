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
            <div class="page-header">
                <h3>View Report</h3>
                <div class="filter-container">
                    <a href="{{ route('reports.edit', $report->id) }}"
                       class="btn btn-primary filter-container__btn mr-1">
                        Edit
                    </a>
                    <button class="btn btn-danger delete-btn" data-id="{{$report->id}}">Delete</button>
                    <a class="btn btn-secondary ml-1" href="{{url(route('reports.index'))}}">Back</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    @include('reports.report_format')
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
        let reportUrl = "{{ url('reports') }}/";
    </script>
    <script src="{{ mix('assets/js/report/report.js') }}"></script>
@endsection
