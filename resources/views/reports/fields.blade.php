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
        {!! Form::text('start_date', null, ['class' => 'form-control','id'=>'start_date','required']) !!}
    </div>

    <!-- End Time Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('end_date', 'End Date') !!}<span class="required">*</span>
        {!! Form::text('end_date', null, ['class' => 'form-control','id'=>'end_date','required']) !!}
    </div>
</div>
<div class="row">
    <!-- Client Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('client_id', 'Client') !!}
        {!! Form::select('client_id', $clients, null, ['class' => 'form-control','id' => 'client','placeholder'=>'Select Client']) !!}
    </div>
</div>
<div class="row">
    <!-- Projects Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('projectIds', 'Project') !!}
        {!! Form::select('projectIds[]', $projects, null, ['class' => 'form-control','id' => 'projectIds','multiple' => true]) !!}
    </div>
</div>
<div class="row">
    <!-- Users Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('users', 'Users') !!}
        {!! Form::select('userIds[]', $users, null, ['class' => 'form-control','id'=>'userIds','multiple' => true]) !!}
    </div>
</div>
<div class="row">
    <!-- tags Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('tags', 'Tags') !!}
        {!! Form::select('tags[]', $tags, null, ['class' => 'form-control','id'=>'tagIds','multiple' => true]) !!}
    </div>

    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('reports.index') !!}" class="btn btn-default">Cancel</a>
    </div>
</div>
