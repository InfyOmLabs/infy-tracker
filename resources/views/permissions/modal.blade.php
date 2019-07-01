<div id="AddModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Permission</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {!! Form::open(['id'=>'addNewForm']) !!}
            <div class="modal-body">
                <div class="alert alert-danger" id="validationErrorsBox" style="display: none"></div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        {!! Form::label('name', 'Name') !!}<span class="required">*</span>
                        {!! Form::text('name', null, ['id'=>'name','class' => 'form-control','required']) !!}
                    </div>
                    <div class="form-group col-sm-6">
                        {!! Form::label('name', 'Display Name') !!}<span class="required">*</span>
                        {!! Form::text('display_name', null, ['id'=>'display_name','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('name', 'Description') !!}
                        {!! Form::textarea('description', null, ['id'=>'description','class' => 'form-control','rows'=>'3']) !!}
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
