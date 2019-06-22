@extends('layouts.app')
@section('title')
    Task Detail
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
            @include('flash::message')
            <div class="page-header">
                <h3>Task detail</h3>
                <div class="filter-container__btn">
                    <button class="btn btn-primary edit-btn" type="button" data-id="{{$task->id}}" data-loading-text="<span class='spinner-border spinner-border-sm'></span> Processing...">Edit Detail</button>
                    <a class="btn btn-secondary" href="{{url(route('tasks.index'))}}">Back</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h4 class="mb-3">{{$task->title}}</h4>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="mb-3"><span
                                                                class="task-detail__heading">Project:</span> {{$task->project->name}}
                                                    </div>
                                                    <div class="mb-3"><span
                                                                class="task-detail__heading">Tags:</span> {{implode(", ",$task->tags->pluck('name')->toArray())}}
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                            <span class="task-detail__heading">
                                                Status:
                                            </span>
                                                        @if($task->status=='0')
                                                            <span class="badge badge-primary text-uppercase">started</span>

                                                        @else
                                                            <span class="badge badge-success text-uppercase">Completed</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="col-lg-12">
                                                <div class="mb-3"><span
                                                            class="task-detail__heading">Assignee:</span> {{implode(", ",$task->taskAssignee->pluck('name')->toArray())}}
                                                </div>
                                                <div class="mb-3"><span
                                                            class="task-detail__heading">Reporter:</span> {{$task->createdUser->name}}
                                                </div>
                                                <div class="mb-3"><span
                                                            class="task-detail__heading">Created At:</span> {{$task->created_at->format('d F, Y h:i A')}}
                                                </div>
                                                <div class="mb-3"><span
                                                            class="task-detail__heading">Updated At:</span> {{$task->updated_at->format('d F, Y h:i A')}}
                                                </div>
                                                <div class="mb-3"><span
                                                            class="task-detail__heading">Priority:</span> <i
                                                            class="fa fa-arrow-up priority-{{$task->priority}}"
                                                            aria-hidden="true"></i> {{ucfirst($task->priority)}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <b>Description</b>
                                        </div>
                                        <div class="col-lg-12">
                                            <p>
                                                {{$task->description}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('tasks.task_edit_modal')
        </div>
    </div>
@endsection
@section('page_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
@endsection
@section('scripts')
    <script>
        let taskUrl = '{{url('tasks')}}/';

    </script>
    <script src="{{ mix('assets/js/task/task_detail.js') }}"></script>
@endsection


