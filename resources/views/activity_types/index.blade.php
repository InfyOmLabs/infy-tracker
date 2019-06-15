@extends('layouts.app')
@section('title')
    Activity Types
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="page-header">
                <h3>Activity Types</h3>
                <div style="display: flex;align-items: center">
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#AddModal"></i>New
                        Activity Type</a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('activity_types.table')
                            <div class="pull-right mr-3">

                            </div>
                        </div>
                        @include('activity_types.modal')
                        @include('activity_types.edit_modal')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
       let activityCreateUrl='{{route('activityTypes.store')}}';
       let activityUrl='{{url('activityTypes')}}/';
    </script>
    <script src="{{ mix('assets/js/activity_types/activity.js') }}"></script>
@endsection

