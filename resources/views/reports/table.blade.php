<div class="table-responsive-sm">
    <div class="list-group">
        @foreach($reports as $key => $report)
            @if($key % 2 ===0 )
                <div class="list-group-item list-group-item-action list-group-item-secondary">
                    <a class="report__heading" href="{!! route('reports.show',$report->id) !!}">
                        {{$report->name}}
                    </a>
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
