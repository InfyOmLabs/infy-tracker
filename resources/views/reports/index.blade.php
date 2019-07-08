@extends('layouts.app')
@section('page_css')
    <link href="{{mix('assets/style/css/report.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="page-header">
                <h3>Reports</h3>
                <div class="filter-container">
                    <a href="{!! route('reports.create') !!}" class="btn btn-primary filter-container__btn">
                        New Report
                    </a>
                </div>
            </div>
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

