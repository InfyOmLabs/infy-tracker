@extends('layouts.app')
@section('title')
    Task Details
@endsection
@section('page_css')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css"/>
    <link href="{{mix('assets/style/css/task-detail.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="page-header">
                <h3>Task Details</h3>
                <div class="filter-container__btn">
                    <button class="btn btn-primary edit-btn" type="button" data-id="{{$task->id}}"
                            data-loading-text="<span class='spinner-border spinner-border-sm'></span> Processing...">
                        Edit Detail
                    </button>
                    <a class="btn btn-secondary" href="{{ url()->previous() }}">Back</a>
                </div>
            </div>
            <div class="row">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="alert alert-danger" id="taskValidationErrorsBox" style="display: none"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <h4 class="mb-3">
                                    <span class="text-info pr-2">{{$task->prefix_task_number}}</span>{{$task->title}}
                                </h4>
                            </div>
                        </div>
                        <div class="row task-detail d-flex">
                            <div class="mb-3 d-flex task-detail__item">
                                <span class="task-detail__project-heading">Project</span>
                                <span class="flex-1">{{$task->project->name}}</span>
                            </div>
                            <div class="mb-3 d-flex task-detail__item">
                                <span class="task-detail__created-heading">Created</span>
                                <span class="flex-1">{{$task->created_at->format('dS F, Y h:i A')}}</span>
                            </div>
                            <div class="mb-3 d-flex task-detail__item">
                                <span class="task-detail__status-heading">Status</span>
                                <span class="flex-1">
                                    @if(isset($taskStatus[$task->status]))
                                        <span class="badge {{$taskBadges[$task->status]}} text-uppercase">{{$taskStatus[$task->status]}}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="mb-3 d-flex task-detail__item">
                                <span class="task-detail__updated-heading">Updated</span>
                                <span class="flex-1">{{$task->updated_at->format('dS F, Y h:i A')}}</span>
                            </div>
                            <div class="mb-3 d-flex task-detail__item">
                                <span class="task-detail__reporter-heading">Reporter</span>
                                <span class="flex-1">{{(isset($task->createdUser->name) ? $task->createdUser->name : '')}}</span>
                            </div>
                            <div class="mb-3 d-flex task-detail__item">
                                <span class="task-detail__priority-heading">Priority</span>
                                <i class="fa fa-arrow-up task-detail__priority-heading--{{$task->priority}}" aria-hidden="true"></i>
                                {{ucfirst($task->priority)}}
                            </div>
                            @if(!empty($task->due_date))
                                <div class="mb-3 d-flex task-detail__item">
                                    <span class="task-detail__due-date-heading">Due Date</span>
                                    <span
                                            class="flex-1">{{\Carbon\Carbon::parse($task->due_date)->format('dS F, Y')}}</span>
                                </div>
                            @endif
                            @if(!empty($task->taskAssignee->pluck('name')->toArray()))
                                <div class="mb-3 d-flex task-detail__item">
                                    <span class="task-detail__assignee-heading">Assignee</span>
                                    <span
                                            class="flex-1">{{implode(", ",$task->taskAssignee->pluck('name')->toArray())}}</span>
                                </div>
                            @endif
                            @if(!empty($task->tags->pluck('name')->toArray()))
                                <div class="mb-3 d-flex task-detail__item">
                                    <span class="task-detail__tag-heading">Tags</span>
                                    <span class="flex-1">{{implode(", ",$task->tags->pluck('name')->toArray())}}</span>
                                </div>
                            @endif
                            @if(!empty($task->timeEntries->isNotEmpty()))
                                <div class="mb-3 d-flex task-detail__item">
                                    <span class="task-detail__tag-heading">Time Tracking</span>
                                    <span class="flex-1"><a class="task-detail__total-time" data-toggle="modal" data-target="#timeTrackingModal"><span data-toggle="tooltip" data-placement="bottom" title="Click for view all entry">{{roundToQuarterHour($task->timeEntries()->sum('duration'))}}</span></a></span>
                                </div>
                            @endif
                        </div>
                        @if(!empty($task->description))
                        <div class="row">
                            <div class="col-lg-8 col-sm-12">
                                <span class="task-detail__description-heading">Description</span>
                            </div>

                            <div class="col-lg-8 col-sm-12">
                                    <div>{!! html_entity_decode($task->description) !!}</div>
                            </div>
                        </div>
                        @else
                            <div class="mb-3 d-flex task-detail__item">
                                <span class="task-detail__due-date-heading">Description</span>
                                <span class="flex-1">N/A</span>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-lg-8 col-sm-12">
                                <div class="mb-3 d-flex">
                                    <span class="task-detail__attachment-heading">Attachments</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-sm-12">
                                <form method="post" action="{{url("tasks/".$task->id."/add-attachment")}}"
                                      enctype="multipart/form-data"
                                      class="dropzone" id="dropzone">
                                    {{csrf_field()}}
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="comments">
                                    <div>
                                        <div class="mb-3 d-flex">
                                            <span class="font-weight-bold">Comments</span>
                                            <span class="flex-1 ml-5 no_comments @if(!($task->comments->isEmpty())) d-none @endif">No comments added yet</span>
                                        </div>
                                    </div>
                                    @foreach($task->comments as $comment)
                                        <div class="comments__information clearfix" id="{{ 'comment__'.$comment->id }}">
                                            <div class="user">
                                                <img class="user__img" src=" {{ $comment->user_avatar }}" alt="User Image">
                                                <span class="user__username">
                                                    <a>{{isset($comment->createdUser->name) ? $comment->createdUser->name : ''}}</a>
                                                    @if($comment->created_by == Auth::id())
                                                        <a class="user__icons del-comment d-none" data-id="{{$comment->id}}"><i class="cui-trash hand-cursor"></i></a>
                                                        <a class="user__icons edit-comment d-none" data-id="{{$comment->id}}"><i class="cui-pencil hand-cursor"></i>&nbsp;</a>
                                                        <a class="user__icons save-comment {{'comment-save-icon-'.$comment->id}} d-none" data-id="{{$comment->id}}"><i class="cui-circle-check text-success font-weight-bold hand-cursor"></i>&nbsp;&nbsp;</a>
                                                        <a class="user__icons cancel-comment {{'comment-cancel-icon-'.$comment->id}} d-none" data-id="{{$comment->id}}"><i class="fa fa-times hand-cursor"></i>&nbsp;&nbsp;</a>
                                                    @endif
                                                </span>
                                                <span class="user__description">{{time_elapsed_string($comment->created_at)}}</span>
                                            </div>
                                            <div class="user__comment @if($comment->created_by == Auth::id()) comment-display @endif {{'comment-display-'.$comment->id}}" data-id="{{$comment->id}}">
                                                {!! html_entity_decode($comment->comment) !!}
                                            </div>
                                            @if($comment->created_by == Auth::id())
                                                <div class="user__comment d-none comment-edit {{'comment-edit-'.$comment->id}}">
                                                    {!! Form::textarea('comment', $comment->comment, ['class' => 'form-control  comment-editor', 'id'=>'comment-edit-'.$comment->id, 'rows' => 4]) !!}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div>
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <strong>{!! Form::label('add_comment', 'Add comment') !!}</strong>
                                            {!! Form::textarea('comment', null, ['class' => 'form-control comment-editor', 'id'=>'comment', 'rows' => 5, 'placeholder' => 'Add a comment...']) !!}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            {!! Form::button('Save', ['type'=>'button','class' => 'btn btn-primary','id'=>'btnComment','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) !!}
                                            <button type="reset" id="btnCancel" class="btn btn-light ml-1">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="previewEle">
            </div>
            @include('tasks.edit_modal')
            @include('tasks.time_tracking_modal')
        </div>
    </div>
@endsection
@section('page_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js.map"></script>
    <script src="https://cdn.ckeditor.com/4.11.4/standard/ckeditor.js"></script>
@endsection
@section('scripts')
    <script>
        let taskUrl = '{{url('tasks')}}/';
        let taskId = '{{$task->id}}';
        let attachmentUrl = '{{ $attachmentUrl }}/';
        let baseUrl = '{{ url('/') }}/';
        let authId = '{{Auth::id()}}';
    </script>
    <script src="{{ mix('assets/js/task/task_detail.js') }}"></script>
@endsection
