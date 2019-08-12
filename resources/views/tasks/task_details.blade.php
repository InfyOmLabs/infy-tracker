<div id="taskDetailsModal" class="modal fade taskDetailsModal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content task-detail__model">
            <div class="modal-header">
                <h5 class="modal-title">Task Time Entries</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body task-detail__body">
                @include('loader')
                <div class="alert alert-info" id="no-record-info-msg">No time entries found for this task</div>
                <div class="row no-gutters" id="taskDetailsTable">
                    <div class="col-4 offset-8 mb-2">
                        {!!Form::select('task_users',[],null,['id'=>'task_users','class'=>'form-control'])  !!}
                    </div>
                    <table class="col-12 table table-responsive-sm table-striped table-bordered">
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
