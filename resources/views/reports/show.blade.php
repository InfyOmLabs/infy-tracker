@extends('layouts.app')
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
                        <div class="card-body reports">
                            <h4>{{$report->name}}</h4>
                            <div class="reports__container">
                                <div class="reports__client-row">
                                    <h5>Scott</h5>
                                    <h5>18hr</h5>
                                </div>
                                <hr class="mt-1 mb-3"/>
                                <div class="reports__project-row">
                                    <div class="reports__project-header">Inventory</div>
                                    <span>6hr</span>
                                </div>
                                <div class="reports__developer-task">
                                    <div class="reports__developer-row">
                                        <div class="reports__developer-header">Dharmesh</div>
                                        <span>6hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">UI Enhacement</span>
                                        <span>6hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">UI Enhacement</span>
                                        <span>6hr</span>
                                    </div>
                                </div>
                                {{--repeated developer-task section--}}
                                <div class="reports__developer-task">
                                    <div class="reports__developer-row">
                                        <div class="reports__developer-header">Dharmesh</div>
                                        <span>6hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">UI Enhacement</span>
                                        <span>6hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">UI Enhacement</span>
                                        <span>6hr</span>
                                    </div>
                                </div>
                            </div>
                            {{--repeated container--}}
                            <div class="reports__container">
                                <div class="reports__client-row">
                                    <h5>Scott</h5>
                                    <h5>18hr</h5>
                                </div>
                                <hr class="mt-1 mb-3"/>
                                <div class="reports__project-row">
                                    <div class="reports__project-header">Inventory</div>
                                    <span>6hr</span>
                                </div>
                                <div class="reports__developer-task">
                                    <div class="reports__developer-row">
                                        <div class="reports__developer-header">Dharmesh</div>
                                        <span>6hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">UI Enhacement</span>
                                        <span>6hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">UI Enhacement</span>
                                        <span>6hr</span>
                                    </div>
                                </div>
                                {{--repeated developer-task section--}}
                                <div class="reports__developer-task">
                                    <div class="reports__developer-row">
                                        <div class="reports__developer-header">Dharmesh</div>
                                        <span>6hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">UI Enhacement</span>
                                        <span>6hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">UI Enhacement</span>
                                        <span>6hr</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
