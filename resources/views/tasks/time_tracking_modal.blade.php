<div class="modal fade" id="timeTrackingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Time Tracking</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover time-tracker-table">
                    <thead>
                    <tr>
                        <th scope="col">Note</th>
                        <th scope="col">Activity</th>
                        <th scope="col">Total Minute(s)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($task->timeEntries as $entry)
                        <tr>
                            <td>{{$entry->note}}</td>
                            <td>{{$entry->activityType->name}}</td>
                            <td>{{$entry->duration}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>