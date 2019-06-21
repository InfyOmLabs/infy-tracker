<div id="EditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Project</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {!! Form::open(['id'=>'editForm','files'=>true]) !!}
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none" id="editValidationErrorsBox"></div>
                {!! Form::hidden('project_id',null,['id'=>'projectId']) !!}
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('name', 'Name') !!}<span class="required">*</span>
                        {!! Form::text('name', '', ['id'=>'edit_name','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('user_id', 'Users') !!}<span class="required">*</span>
                        {!! Form::select('user_ids[]', $users, null, ['id' => 'edit_user_ids','class' => 'form-control', 'required', 'multiple']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('client', 'Client') !!}<span class="required">*</span>
                        {!! Form::select('client_id', $clients, null, ['id'=>'edit_client_id','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('description', 'Description') !!}
                        {!! Form::textarea('description', '', ['id'=>'edit_description', 'class' => 'form-control', 'rows' => 5]) !!}
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
