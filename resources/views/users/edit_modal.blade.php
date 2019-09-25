<div id="EditModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {!! Form::open(['id'=>'editForm','files'=>true]) !!}
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none" id="editValidationErrorsBox"></div>
                {!! Form::hidden('user_id',null,['id'=>'userId']) !!}
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('name', 'Name') !!}<span class="required">*</span>
                        {!! Form::text('name', null, ['id'=>'edit_name','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('email', 'Email') !!}<span class="required">*</span>
                        {!! Form::email('email', null, ['id'=>'edit_email','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('phone', 'Phone') !!}
                        {!! Form::number('phone', null, ['id'=>'edit_phone','class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('project_id', 'Project') !!}
                        {!! Form::select('project_ids[]', $projects, null, ['class' => 'form-control','id' => 'editProjectId', 'multiple'=>true]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('active', 'Active') !!}
                        <label class="switch switch-label switch-outline-primary-alt d-block">
                            <input name="is_active" class="switch-input" id="edit_is_active" type="checkbox">
                            <span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('role_id', 'Role') !!}
                        {!! Form::select('role_id', $roles, null, ['class' => 'form-control', 'id' => 'editRoleId']) !!}
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
