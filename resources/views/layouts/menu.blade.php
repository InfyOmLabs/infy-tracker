<li class="nav-item {{ Request::is('home*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('home') !!}"> 
        <i class="fas fa-tachometer-alt nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Dashboard
    </a>
</li>

@can('manage_department')
    <li class="nav-item {{ Request::is('departments*') ? 'active' : '' }}">
        <a class="nav-link" href="{!! route('departments.index') !!}"> 
            <i class="nav-icon fa fa-building"></i> Departments 
        </a>
    </li>
@endcan

@can('manage_clients')
    <li class="nav-item {{ Request::is('clients*') ? 'active' : '' }}">
        <a class="nav-link" href="{!! route('clients.index') !!}"> 
            <i class="fas fa-user-tie nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Clients 
        </a>
    </li>
@endcan

@can('manage_projects')
<li class="nav-item {{ Request::is('projects*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('projects.index') !!}">
        <i class="fa fa-folder-open nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Projects
    </a>
</li>
@endcan

@can('manage_all_tasks')
<li class="nav-item {{ Request::is('tasks*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('tasks.index') !!}">
        <i class="fa fa-tasks nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Tasks
    </a>
</li>
@endcan

<li class="nav-item {{ Request::is('time-entries*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('time-entries.index') !!}">
        <i class="fas fa-user-clock nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Time Entries
    </a>
</li>
@can('manage_reports')
<li class="nav-item {{ Request::is('report*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! url('reports') !!}">
        <i class="fa fa-file nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Reports
    </a>
</li>
@endcan

@can('manage_users')
    <li class="nav-item {{ Request::is('users*') ? 'active' : '' }}">
        <a class="nav-link" href="{!! route('users.index') !!}">
            <i class="fa fa-users nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Users
        </a>
    </li>
@endcan
@can('manage_roles')
<li class="nav-item {{ Request::is('roles*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! url('roles') !!}">
        <i class="fa fa-user nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Roles
    </a>
</li>
@endcan

@canany(['manage_activities','manage_tags'])
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="fa fa-cog nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Setting
    </a>
    <ul class="nav-dropdown-items">
        @can('manage_tags')
            <li class="nav-item {{ Request::is('tags*') ? 'active' : '' }}">
                <a class="nav-link" href="{!! route('tags.index') !!}">
                    <i class="fa fa-tags nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Tags
                </a>
            </li>
        @endcan
        @can('manage_activities')
            <li class="nav-item {{ Request::is('activity-types*') ? 'active' : '' }}">
                <a class="nav-link" href="{!! route('activity-types.index') !!}">
                    <i class="fas fa-clipboard-list nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Activity Types
                </a>
            </li>
        @endcan
    </ul>
</li>
@endcan
