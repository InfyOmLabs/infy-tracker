<!-- Name Field -->
<div class="row">
    <div class="form-group col-sm-6">
        {!! Form::label('name', 'Name') !!}<span class="required">*</span>
        {!! Form::text('name', null, ['class' => 'form-control','required']) !!}
    </div>
</div>

<div class="row">
    <!-- Start Time Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('start_date', 'Start Date') !!}<span class="required">*</span>
        {!! Form::text('start_date', null, ['class' => 'form-control','id'=>'start_date','required', 'autocomplete' => 'off']) !!}
    </div>

    <!-- End Time Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('end_date', 'End Date') !!}<span class="required">*</span>
        {!! Form::text('end_date', null, ['class' => 'form-control','id'=>'end_date','required', 'autocomplete' => 'off']) !!}
    </div>
</div>
<div class="row">
    <!-- Department Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('department_id', 'Department') !!}
        {!! Form::select('department_id', $departments, isset($departmentId)?$departmentId:null, ['class' => 'form-control','id' => 'department','placeholder'=>'Select Department']) !!}
    </div>
</div>
<div class="row">
    <!-- Client Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('clientId', 'Client') !!}
        {!! Form::select('client_id', $clients, isset($clientId)?$clientId:null, ['class' => 'form-control','id' => 'clientId','placeholder'=>'Select client']) !!}
    </div>
</div>
<div class="row">
    <!-- Projects Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('projectIds', 'Project') !!}
        {!! Form::select('projectIds[]', $projects, isset($projectIds)?$projectIds:null, ['class' => 'form-control','id' => 'projectIds','multiple' => true]) !!}
    </div>
</div>
<div class="row">
    <!-- Users Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('users', 'Users') !!}
        {!! Form::select('userIds[]', $users, isset($userIds)?$userIds:null, ['class' => 'form-control','id'=>'userIds','multiple' => true]) !!}
    </div>
</div>
<div class="row">
    <!-- tags Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('tags', 'Tags') !!}
        {!! Form::select('tagIds[]', $tags,isset($tagIds)?$tagIds:null, ['class' => 'form-control','id'=>'tagIds','multiple' => true]) !!}
    </div>

    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('reports.index') !!}" class="btn btn-secondary">Cancel</a>
    </div>
</div>
