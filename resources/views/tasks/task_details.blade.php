<div id="taskDetailsModal" class="modal fade taskDetailsModal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content task-detail__model">
            <div class="modal-header">
                <h5 class="modal-title">Task Time Entries</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" style="overflow: auto">
                @include('loader')
                <div class="alert alert-info" id="no-record-info-msg">No time entries found for this task</div>
                <div class="mb-2">
                    {!!Form::select('task_users',[],null,['id'=>'task_users','class'=>'form-control','style'=>'min-width:300px;', 'placeholder' => 'Select Users'])  !!}
                </div>
                <table class="table table-responsive-sm table-striped table-bordered" id="taskDetailsTable">
                    <thead>
                    <tr>
                        <th></th>
                        <th>User</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Duration</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
