<div id="EditModal" class="modal fade" role="dialog" >
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Task</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {!! Form::open(['id'=>'editForm']) !!}
            <div class="modal-body">
                <div class="alert alert-danger" id="editValidationErrorsBox"style="display: none" ></div>
                {!! Form::hidden('tag_id',null,['id'=>'tagId']) !!}
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('project_id', 'Project') !!}<span class="required">*</span>
                        {!! Form::select('project_id', $projects, null, ['class' => 'form-control','required', 'id' => 'editProjectId', 'placeholder'=>'Select Project']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('title', 'Title') !!}<span class="required">*</span>
                        {!! Form::text('title', null, ['id'=>'editTitle','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('priority', 'Priority') !!}<span class="required">*</span>
                        {!! Form::select('priority',$priority, null, ['class' => 'form-control','id'=>'editPriority','required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('assign_to', 'Assign To') !!}
                        {!! Form::select('assignees[]',$assignees, null, ['class' => 'form-control','id'=>'editAssignee', 'multiple' => true]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('due_date', 'Due Date') !!}
                        {!! Form::text('due_date', null, ['id'=>'editDueDate','class' => 'form-control', 'autocomplete' => 'off']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('description', 'Description') !!}
                        {!! Form::textarea('description', null, ['id' => 'editDesc', 'class' => 'form-control', 'rows' => 5]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('tags', 'Tags') !!}
                        {!! Form::select('tags[]',$tags, null, ['class' => 'form-control','id'=>'editTagIds', 'multiple' => true]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12" style="display: flex">
                        <div>
                            {!! Form::checkbox('status', 1, false, ['id' => 'editStatus', 'class' => 'chkStatus']) !!}
                        </div>
                        <div style="margin-left: 5px">
                            Completed
                        </div>
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
