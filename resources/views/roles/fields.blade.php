<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name') !!}<span class="required"> *</span>
    {!! Form::text('name', null, ['class' => 'form-control','required']) !!}
</div>

<!-- Detail Field -->
<div class="form-group col-sm-6 col-lg-6">
    {!! Form::label('detail', 'Description') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control','rows'=>5]) !!}
</div>


<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('detail', 'Permissions') !!}
    <div class="row">
        @foreach($permissions as $key=>$value)
            <div class="col-lg-2">
                <label class="vertical-align"><input name="permissions[]" type="checkbox" class="permission-checkbox" value="{{$key}}"> {{$value}}</label>
            </div>
        @endforeach
    </div>

</div>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('roles.index') !!}" class="btn btn-default">Cancel</a>
</div>
