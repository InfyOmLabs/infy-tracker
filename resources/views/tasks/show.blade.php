@extends('layouts.app')
@section('title')
    Task Detail
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
                <h3>Task detail</h3>
                <div class="filter-container__btn">
                    <button class="btn btn-primary edit-btn" type="button" data-id="{{$task->id}}"
                            data-loading-text="<span class='spinner-border spinner-border-sm'></span> Processing...">
                        Edit Detail
                    </button>
                    <a class="btn btn-secondary" href="{{url(route('tasks.index'))}}">Back</a>
                </div>
            </div>
            <div class="row">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="alert alert-danger" id="taskValidationErrorsBox" style="display: none"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <h4 class="mb-3">
                                    <span class="text-info pr-2">#{{$task->task_number}}</span>{{$task->title}}
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
                                    @if($task->status=='0')
                                        <span class="badge badge-primary text-uppercase">Started</span>
                                    @else
                                        <span class="badge badge-success text-uppercase">Completed</span>
                                    @endif
                                </span>
                            </div>
                            <div class="mb-3 d-flex task-detail__item">
                                <span class="task-detail__updated-heading">Updated</span>
                                <span class="flex-1">{{$task->updated_at->format('dS F, Y h:i A')}}</span>
                            </div>
                            <div class="mb-3 d-flex task-detail__item">
                                <span class="task-detail__reporter-heading">Reporter</span>
                                <span class="flex-1">{{$task->createdUser->name}}</span>
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
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-sm-12">
                                <span class="task-detail__description-heading">Description</span>
                            </div>
                            <div class="col-lg-8 col-sm-12">
                                <span>{{$task->description}}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-sm-12">
                                <div class="mb-3 d-flex">
                                    <span class="task-detail__attachment-heading">Attachments</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-sm-12">
                                <form method="post" action="{{url("tasks/add-attachment/$task->id")}}"
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
                                        </div>
                                    </div>
                                    @foreach($task->comments as $comment)
                                        <div class="comments__information clearfix" id="{{ 'comment__'.$comment->id }}">
                                            <div class="user">
                                                <img class="user__img" src="{{url('/assets/img/user-avatar.png')}}" alt="User Image">
                                                <span class="user__username">
                                                    <a>{{$comment['createdUser']->name}}</a>
                                                    @if($comment->created_by == Auth::id())
                                                        <a class="pull-right del-comment d-none" data-id="{{$comment->id}}"><i class="cui-trash"></i></a>
                                                        <a class="pull-right edit-comment {{'comment-edit-icon-'.$comment->id}} d-none" data-id="{{$comment->id}}"><i class="cui-pencil"></i>&nbsp;&nbsp;</a>
                                                        <a class="pull-right cancel-comment {{'comment-cancel-icon-'.$comment->id}} d-none" data-id="{{$comment->id}}"><i class="fa fa-times"></i>&nbsp;&nbsp;</a>
                                                    @endif
                                                </span>
                                                <span class="user__description">{{time_elapsed_string($comment->created_at)}}</span>
                                            </div>
                                            <div class="user__comment @if($comment->created_by == Auth::id()) comment-display @endif {{'comment-display-'.$comment->id}}" data-id="{{$comment->id}}">
                                                <?php echo html_entity_decode($comment->comment) ?>
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
                                        <div class="form-group col-sm-8">
                                            {!! Form::textarea('comment', null, ['class' => 'form-control comment-editor', 'id'=>'comment', 'rows' => 5, 'placeholder' => 'Add a comment...']) !!}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            {!! Form::button('Save', ['type'=>'button','class' => 'btn btn-primary','id'=>'btnComment','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) !!}
                                            <button type="button" id="btnCancel" class="btn btn-light ml-1" data-dismiss="modal">Cancel</button>
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
            @include('tasks.task_edit_modal')
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
