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
                    {!! Form::open(['route' => ['reports.destroy', $report->id], 'method' => 'delete']) !!}
                    {!! Form::button('Delete', ['type' => 'submit', 'class' => 'btn btn-danger',]) !!}
                    {!! Form::close() !!}
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
