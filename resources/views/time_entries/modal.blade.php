<div id="timeEntryAddModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Time Entry</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {!! Form::open(['id'=>'timeEntryAddForm']) !!}
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none" id="tmValidationErrorsBox"></div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('project', 'Project') !!}<span class="required">*</span>
                        {!! Form::select('project_id', $projects, null, ['id'=>'timeProjectId','class' => 'form-control','required', 'placeholder'=>'Select Project']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('task', 'Task') !!}<span class="required">*</span>
                        {!! Form::select('task_id', [], null, ['id'=>'taskId','class' => 'form-control','required', 'placeholder'=>'Select Task']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('Activity Type', 'Activity Type') !!}<span class="required">*</span>
                        {!! Form::select('activity_type_id', $activityTypes, null, ['id'=>'activityTypeId','class' => 'form-control','required', 'placeholder'=>'Select Activity']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        {!! Form::label('start_time', 'Start Time') !!}<span class="required">*</span>
                        <div id="dvStartTime">
                            {!! Form::text('start_time', null, ['class' => 'form-control','id'=>'startTime', 'autocomplete' => 'off','required']) !!}
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        {!! Form::label('end_time', 'End Time') !!}<span class="required">*</span>
                        <div id="dvEndTime">
                            {!! Form::text('end_time', null, ['class' => 'form-control','id'=>'endTime', 'autocomplete' => 'off', 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('duration', 'Duration (minutes)') !!}
                        <div id="dvDuration">
                            {!! Form::number('duration', null, ['class' => 'form-control','id' => 'duration', 'readonly']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('note', 'Note') !!}
                        {!! Form::textarea('note', null, ['class' => 'form-control', 'rows' => 5]) !!}
                    </div>
                </div>
                <div class="text-right">
                    {!! Form::button('Save', ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) !!}
                    <button type="button" id="btnCancel" class="btn btn-light ml-1" data-dismiss="modal">Cancel</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
