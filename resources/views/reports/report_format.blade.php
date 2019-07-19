<div class="card">
    <div class="card-body reports">
        <h4>{{$report->name}} ({{$totalHours}})</h4>
        <h2>{{ $report->formatted_date }}</h2>
        @if(empty($reports))
            <div class="d-flex justify-content-center">
                <span>No record available.</span>
            </div>
        @endif
        @foreach($reports as $client)
            <div class="reports__container">
                <div class="reports__client-row">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie mr-2"></i>
                        {{ucwords($client['name'])}}
                    </h5>
                    <h5 class="mb-0">
                        {{$client['time']}} ({{round($client['duration'] * 100 / $totalMinutes, 2)}} %)
                    </h5>
                </div>
                <hr class="my-0"/>
                @foreach($client['projects'] as $project)
                    <div class="reports__project-row">
                        <div class="reports__project-header">
                            <i class="fa fa-folder-open mr-2"></i>
                            {{ucwords($project['name'])}}
                        </div>
                        <span>{{$project['time']}} ({{round($project['duration'] * 100 / $client['duration'], 2)}} %)</span>
                    </div>
                    @foreach($project['users'] as $user)
                        <div class="reports__developer-task">
                            <div class="reports__developer-row">
                                <div class="reports__developer-header">
                                    <i class="fa fa-users mr-2"></i>
                                    {{ucwords($user['name'])}}
                                </div>
                                <span>{{$user['time']}} ({{round($user['duration'] * 100 / $project['duration'], 2)}} %)</span>
                            </div>
                            @foreach($user['tasks'] as $task)
                                <div class="reports__task-row">
                                                        <span class="reports__task-header">
                                                          {{$task['name']}}
                                                        </span>
                                    <span>{{$task['time']}}</span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endforeach
            </div>
        @endforeach
    </div>
</div>
