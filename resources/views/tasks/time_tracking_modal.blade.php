<div class="modal fade" id="timeTrackingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Time Tracking</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body overflow-auto">
                <table class="table table-hover time-tracker-table">
                    <thead>
                    <tr>
                        <th scope="col">Note</th>
                        <th scope="col">Activity</th>
                        <th scope="col">Start Time</th>
                        <th scope="col">End Time</th>
                        <th scope="col" class="text-nowrap text-center">Tracked By</th>
                        <th scope="col" class="text-nowrap text-center">Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($task->timeEntries as $entry)
                        <tr>
                            <td>{!! nl2br($entry->note) !!}</td>
                            <td>{{$entry->activityType->name}}</td>
                            <td>{{\Carbon\Carbon::parse($entry->start_time)->format('Y-m-d h:ma')}}</td>
                            <td>{{\Carbon\Carbon::parse($entry->end_time)->format('Y-m-d h:ma')}}</td>
                            <td class="text-nowrap text-center"><img src="{{$entry->user->img_avatar}}" width="40px"> </td>
                            <td class="text-nowrap text-center">{{roundToQuarterHour($entry->duration)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
