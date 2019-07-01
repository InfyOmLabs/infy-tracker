<div id="EditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Permission</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {!! Form::open(['id'=>'editForm']) !!}
            <div class="modal-body">
                <div class="alert alert-danger" id="editValidationErrorsBox" style="display: none"></div>
                {!! Form::hidden('permission_id',null,['id'=>'permissionId']) !!}
                <div class="row">
                    <div class="form-group col-sm-6">
                        {!! Form::label('name', 'Name') !!}<span class="required">*</span>
                        {!! Form::text('name', null, ['id'=>'editName','class' => 'form-control','required']) !!}
                    </div>
                    <div class="form-group col-sm-6">
                        {!! Form::label('name', 'Display Name') !!}<span class="required">*</span>
                        {!! Form::text('display_name', null, ['id'=>'editDisplayName','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('name', 'Description') !!}
                        {!! Form::textarea('description', null, ['id'=>'editDescription','class' => 'form-control','rows'=>'3']) !!}
                    </div>
                </div>
                <div class="text-right">
                    {!! Form::button('Save', ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnEditSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) !!}
                    <button type="button" id="btnCancel" class="btn btn-light ml-1" data-dismiss="modal">Cancel</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
