<div class="table-responsive-sm">
    <div class="list-group">
        @foreach($reports as $key => $report)
            @php
                $className  = ($key % 2 == 0) ?  'list-group-item-secondary' : 'list-group-item-light'
            @endphp
                <div class="list-group-item list-group-item-action {{ $className }}">
                    <div class="row">
                        <div class="col-11">
                            <a class="report__heading" href="{!! route('reports.show',$report->id) !!}">
                                {{$report->name}}
                            </a>
                        </div>
                        <div class="col-1 text-right">
                            <a title="Edit" href="{!! route('reports.edit',$report->id) !!}" class="btn action-btn btn-primary btn-sm mr-1"><i class="cui-pencil action-icon"></i></a>
                            <a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn"
                               data-id="{{$report->id}}"><i class="cui-trash action-icon text-danger"></i></a>
                        </div>
                    </div>
                </div>
        @endforeach
    </div>
</div>
