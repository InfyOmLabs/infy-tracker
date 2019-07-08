@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('coreui-templates::common.errors')
            <div class="page-header">
                <h3>View Report</h3>
                <div class="filter-container">
                    <a href="{!! route('reports.edit',$report->id) !!}}"
                       class="btn btn-primary filter-container__btn mr-1">
                        Edit
                    </a>
                    <a href="{!! route('reports.destroy',$report->id) !!}}"
                       class="btn btn-danger filter-container__btn mr-1">
                        Delete
                    </a>
                    <a class="btn btn-secondary" href="{{url(route('reports.index'))}}">Back</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>{{$report->name}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
