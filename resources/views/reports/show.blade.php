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
                    <a href="{{ route('reports.edit', $report->id) }}"
                       class="btn btn-primary filter-container__btn mr-1">
                        Edit
                    </a>
                    <a href="{{ route('reports.destroy', $report->id) }}"
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
                                    <h5 class="mb-0"><i class="fas fa-user-tie mr-2"></i>Scott</h5>
                                    <h5 class="mb-0">18hr</h5>
                                </div>
                                <hr class="my-0"/>
                                <div class="reports__project-row">
                                    <div class="reports__project-header"><i class="fa fa-folder-open mr-2"></i>Inventory
                                    </div>
                                    <span>10hr</span>
                                </div>
                                <div class="reports__developer-task">
                                    <div class="reports__developer-row">
                                        <div class="reports__developer-header"><i class="fa fa-users mr-2"></i>Dharmesh
                                        </div>
                                        <span>6hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">INVENTORY-1714 Asset Model Implemented</span>
                                        <span>3hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">INVENTORY-1780 officespot affiliate support added</span>
                                        <span>3hr</span>
                                    </div>
                                </div>
                                {{--repeated developer-task section--}}
                                <div class="reports__developer-task">
                                    <div class="reports__developer-row">
                                        <div class="reports__developer-header"><i class="fa fa-users mr-2"></i>Shailsh
                                        </div>
                                        <span>4hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">INVENTORY-1223 Print label: add locations after warehouse setup</span>
                                        <span>3hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">INVENTORY-1780 User should only be able to perform Audit for one Site</span>
                                        <span>1hr</span>
                                    </div>
                                </div>

                                <div class="reports__project-row">
                                    <div class="reports__project-header"><i class="fa fa-folder-open mr-2"></i>OR</div>
                                    <span>7hr</span>
                                </div>
                                <div class="reports__developer-task">
                                    <div class="reports__developer-row">
                                        <div class="reports__developer-header"><i class="fa fa-users mr-2"></i>Monika
                                        </div>
                                        <span>8hr</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">OR-122 Asset model support add into order request</span>
                                        <span>4hr 30min</span>
                                    </div>
                                    <div class="reports__task-row">
                                        <span class="reports__task-header">OR-121 Status change notification sent to asset and inventory managers</span>
                                        <span>3hr 30min</span>
                                    </div>
                                </div>
                            </div>
                            {{--repeated container--}}
                            <div class="reports__container">
                                <div class="reports__client-row">
                                    <h5 class="mb-0"><i class="fas fa-user-tie mr-2"></i>Scott</h5>
                                    <h5 class="mb-0">18hr</h5>
                                </div>
                                <hr class="my-0"/>
                                <div class="reports__project-row">
                                    <div class="reports__project-header"><i class="fa fa-folder-open mr-2"></i>Inventory
                                    </div>
                                    <span>6hr</span>
                                </div>
                                <div class="reports__developer-task">
                                    <div class="reports__developer-row">
                                        <div class="reports__developer-header"><i class="fa fa-users mr-2"></i>Dharmesh
                                        </div>
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
                                        <div class="reports__developer-header"><i class="fa fa-users mr-2"></i>Dharmesh
                                        </div>
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
