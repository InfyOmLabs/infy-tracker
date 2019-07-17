<div class="table-responsive-sm">
    <div class="list-group">
        @foreach($reports as $key => $report)
            @if($key % 2 ===0 )
                <div class="list-group-item list-group-item-action list-group-item-secondary">
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
            @else
                <div class="list-group-item list-group-item-action list-group-item-light">
                    <a class="report__heading" href="{!! route('reports.show',$report->id) !!}">
                        {{$report->name}}
                    </a>
                </div>
            @endif
        @endforeach
    </div>
</div>
