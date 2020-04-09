<div id="EditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Client</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {!! Form::open(['id' => 'editForm', 'files' => true]) !!}
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none" id="editValidationErrorsBox"></div>
                {!! Form::hidden('client_id', null, ['id' => 'clientId']) !!}
                <div class="row">
                    <div class="form-group col-sm-6">
                        {!! Form::label('name', 'Name') !!}<span class="required">*</span>
                        {!! Form::text('name', '', ['id' => 'edit_name', 'class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group col-sm-6">
                        {!! Form::label('department_id', 'Department') !!}<span class="required">*</span>
                        {!! Form::select('department_id', $departments, null, ['id' => 'edit_department_id', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('email', 'Email') !!}
                        {!! Form::email('email', '', ['id' => 'edit_email', 'class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('website', 'Website') !!}
                        {!! Form::url('website', '', ['id' => 'edit_website', 'class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="text-right">
                    {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnEditSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) !!}
                    <button type="button" class="btn btn-light ml-1" data-dismiss="modal">Cancel</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
