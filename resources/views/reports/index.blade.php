@extends('layouts.app')
@section('title')
    Reports
@endsection
@section('page_css')
    <link href="{{mix('assets/style/css/report.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="page-header">
                <h3>Reports</h3>
                <div class="filter-container">
                    <div class="mr-2">
                        <label class="lbl-block"><b>Created By</b></label>
                        {!!Form::select('created_by',$users,null,['id'=>'filterCreatedBy','class'=>'form-control','style'=>'min-width:150px;', 'placeholder' => 'All'])  !!}
                    </div>
                    <a href="{!! route('reports.create') !!}" class="btn btn-primary filter-container__btn">
                        New Report
                    </a>
                </div>
            </div>
            @include('flash::message')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('reports.table')
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
        let reportUrl = "{{ url('reports') }}/";
    </script>
    <script src="{{ mix('assets/js/report/report.js') }}"></script>
@endsection

