<div class="card">
    <div class="card-body reports">
    <div class="page-header mt-0">
        <h4>{{$report->name}} ({{$totalHours}})</h4>
        <div class="text-right">
            <h4>{{$report->formatted_date}}</h4>
        </div>
    </div>
        @if(empty($reports))
            <div class="d-flex justify-content-center">
                <span>No record available.</span>
            </div>
        @endif
        @foreach($reports as $client)
            <div class="reports__container">
                <div class="reports__client-row">
                    <h5 class="mb-0 reports__client-row-title">
                        <i class="fas fa-caret-up mr-1"></i>
                        <i class="fas fa-user-tie mr-2"></i>
                        {{ucwords($client['name'])}}
                    </h5>
                    <h5 class="mb-0">
                        {{$client['time']}} ({{round($client['duration'] * 100 / $totalMinutes, 2)}} %)
                    </h5>
                </div>
                <hr class="my-0"/>
                <div class="collapse-row">
                @foreach($client['projects'] as $project)
                    <div class="reports__project-row">
                        <div class="reports__project-header">
                            <i class="fas fa-caret-up mr-1"></i>
                            <i class="fa fa-folder-open mr-2"></i>
                            {{ucwords($project['name'])}}
                        </div>
                        <span>{{$project['time']}} ({{round($project['duration'] * 100 / $client['duration'], 2)}} %)</span>
                    </div>
                    <div class="reports__project-container">
                    @foreach($project['users'] as $user)
                        <div class="reports__developer-task">
                            <div class="reports__developer-row">
                                <div class="reports__developer-header">
                                    <i class="fas fa-caret-up mr-1"></i>
                                    <i class="fa fa-users mr-2"></i>
                                    {{ucwords($user['name'])}}
                                </div>
                                <span>{{$user['time']}} ({{round($user['duration'] * 100 / $project['duration'], 2)}} %)</span>
                            </div>
                            <div class="reports__task-container">
                                @foreach($user['tasks'] as $task)
                                    <div class="reports__task-row">
                                                        <span class="reports__task-header">
                                                          {{$task['name']}}
                                                        </span>
                                        <span>{{$task['time']}}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    </div>
                @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

