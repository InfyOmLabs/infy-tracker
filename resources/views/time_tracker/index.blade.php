<img class="img-stopwatch" id="imgTimer" alt="" src={{asset('assets/img/stopwatch.png')}}>
<div class="chat-popup card-body" id="timeTracker" style="display: none">
    {!! Form::open(['id'=>'timeTrackerForm', 'class' => 'time-tracker-form']) !!}
    <div class="modal-body time-tracker-modal">
        <div class="alert alert-danger" style="display: none" id="timeTrackerValidationErrorsBox"></div>
        <div class="row">
            <div class="form-group col-sm-4">
                {!! Form::label('project_id', 'Project') !!}<span class="required">*</span>
                {!! Form::select('project_id', [], null, ['id' => 'tmProjectId','class' => 'form-control', 'placeholder' => 'Select Project', 'required']) !!}
            </div>
            <div class="form-group col-sm-4">
                {!! Form::label('task_id', 'Task') !!}<span class="required">*</span>
                {!! Form::select('task_id', [], null, ['id' => 'tmTaskId','class' => 'form-control', 'placeholder' => 'Select Task', 'required']) !!}
            </div>
            <div class="form-group col-sm-4">
                {!! Form::label('activity_type_id', 'Activity Type') !!}<span class="required">*</span>
                {!! Form::select('activity_type_id', [], null, ['id'=>'tmActivityId','class' => 'form-control', 'placeholder' => 'Select Activity', 'required']) !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-9">
                {!! Form::label('notes', 'Notes') !!} <span id="tmNotesErr" style="color: red"></span>
                {!! Form::textarea('note', null, ['class' => 'form-control', 'id' => 'tmNotes', 'rows' => 5]) !!}
            </div>
            <div class="form-group col-sm-3">
                <div style="margin-top: 52px">
                    <h3 id="timer"><b>00:00:00</b></h3>
                    <div style="margin-left: 40px">
                        <button class="btn btn-success time-tracker-form__btn" id="startTimer">
                            <i id="startTimeTracker" class="far fa-play-circle"></i> Start
                        </button>
                        <button class="btn btn-danger time-tracker-form__btn" id="stopTimer" style="display: none;">
                            <i id="stopTimeTracker" class="far fa-stop-circle"></i> Stop
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
