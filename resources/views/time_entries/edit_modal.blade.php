<div id="editTimeEntryModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Time Entry</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {!! Form::open(['id'=>'editTimeEntryForm','class'=>'editTimeEntryForm','files'=>true]) !!}
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none" id="teEditValidationErrorsBox"></div>
                {!! Form::hidden('entry_id',null,['id'=>'entryId']) !!}
                <div class="form-group row">
                    <div class="col-sm-4">
                        {!! Form::label('project', 'Project') !!}<span class="required">*</span>
                        {!! Form::select('project_id', $projects, null, ['id'=>'editTimeProjectId','class' => 'form-control','required', 'placeholder'=>'Select Project']) !!}
                    </div>
                    <div class="col-sm-8">
                        {!! Form::label('task', 'Task') !!}<span class="required">*</span>
                        {!! Form::select('task_id', $tasks, null, ['id'=>'editTaskId','class' => 'form-control','required','placeholder'=>'Select Task']) !!}
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-4">
                        {!! Form::label('start_time', 'Start Time') !!}<span class="required">*</span>
                        <div id="dvEditStartTime">
                            {!! Form::text('start_time', null, ['class' => 'form-control','id'=>'editStartTime', 'autocomplete' => 'off', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-sm-8">
                        {!! Form::label('Activity Type', 'Activity Type') !!}<span class="required">*</span>
                        {!! Form::select('activity_type_id', $activityTypes, null, ['id'=>'editActivityTypeId','class' => 'form-control','required','placeholder'=>'Select Task']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 p-0">
                        <div class="form-group col-sm-12">
                            {!! Form::label('end_time', 'End Time') !!}<span class="required">*</span>
                            <div id="dvEditEndTime">
                                {!! Form::text('end_time', null, ['class' => 'form-control','id'=>'editEndTime', 'autocomplete' => 'off', 'required']) !!}
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            {!! Form::label('duration', 'Duration (minutes)') !!}
                            <div id="dvEditDuration">
                                {!! Form::number('duration', null, ['class' => 'form-control','id' => 'editDuration', 'readonly']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-8">
                        {!! Form::label('note', 'Note') !!}
                        {!! Form::textarea('note', null, ['class' => 'form-control','id' => 'editNote', 'rows' => 5, 'style'=>'line-height: 1.5;']) !!}
                    </div>
                </div>
                <div class="text-right">
                    {!! Form::button('Save', ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnEditSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) !!}
                    <button type="button" class="btn btn-light ml-1" data-dismiss="modal">Cancel</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
