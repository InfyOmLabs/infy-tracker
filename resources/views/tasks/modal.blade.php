<div id="AddModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Task</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {!! Form::open(['id'=>'addNewForm']) !!}
            <div class="modal-body">
                <div class="alert alert-danger" id="validationErrorsBox" style="display: none"></div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        {!! Form::label('title', 'Title') !!}<span class="required">*</span>
                        {!! Form::text('title', null, ['id'=>'title','class' => 'form-control','required']) !!}
                    </div>
                    <div class="form-group col-sm-6">
                        {!! Form::label('project_id', 'Project') !!}<span class="required">*</span>
                        {!! Form::select('project_id', $projects, null, ['class' => 'form-control','required', 'id' => 'projectId', 'placeholder'=>'Select Project']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        {!! Form::label('priority', 'Priority') !!}<span class="required">*</span>
                        {!! Form::select('priority',$priority, null, ['class' => 'form-control','id'=>'priority','required','placeholder'=>'Select Priority']) !!}
                    </div>
                    <div class="form-group col-sm-6">
                        {!! Form::label('assign_to', 'Assign To') !!}
                        {!! Form::select('assignees[]',[], null, ['class' => 'form-control','id'=>'assignee', 'multiple' => true]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        {!! Form::label('due_date', 'Due Date') !!}
                        {!! Form::text('due_date', null, ['id'=>'dueDate','class' => 'form-control', 'autocomplete' => 'off']) !!}
                    </div>
                    <div class="form-group col-sm-6">
                        {!! Form::label('tags', 'Tags') !!}
                        {!! Form::select('tags[]',$tags, null, ['class' => 'form-control','id'=>'tagIds', 'multiple' => true]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div id="taskDescriptionContainer" class="quill-editor-container"></div>
                        <input type="hidden" name="description" id="taskDescription">
                    </div>
                </div>
                <div class="text-right">
                    {!! Form::button('Save', ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnTaskSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) !!}
                    <button type="button" class="btn btn-light ml-1" data-dismiss="modal">Cancel</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
