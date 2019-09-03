<div id="timeEntryAdjustModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Time Entry</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {!! Form::open(['id'=>'timeEntryAdjustForm']) !!}
            <div class="modal-body">
                <div class="alert alert-warning" style="display: none" id="tmAdjustValidationErrorsBox"></div>
                <div class="form-group row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('start_time', 'Start Time') !!}<span class="required">*</span>
                        <div id="dvAdjustStartTime">
                            {!! Form::text('start_time', null, ['class' => 'form-control','id'=>'adjustStartTime', 'autocomplete' => 'off','required']) !!}
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        {!! Form::label('end_time', 'End Time') !!}<span class="required">*</span>
                        <div id="dvAdjustEndTime">
                            {!! Form::text('end_time', null, ['class' => 'form-control','id'=>'adjustEndTime', 'autocomplete' => 'off', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        {!! Form::label('duration', 'Duration (minutes)') !!}
                        <div id="dvAdjustDuration">
                            {!! Form::number('duration', null, ['class' => 'form-control','id' => 'adjustDuration', 'readonly']) !!}
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    {!! Form::button('Save', ['type'=>'button','class' => 'btn btn-primary','id'=>'adjustBtnSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) !!}
                    <button type="button" id="adjustBtnCancel" class="btn btn-light ml-1" data-dismiss="modal">Cancel</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
