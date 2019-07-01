<li class="nav-item {{ Request::is('home*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('home') !!}">
        <i class="fas fa-tachometer-alt nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Dashboard
    </a>
</li>

<li class="nav-item {{ Request::is('clients*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('clients.index') !!}">
        <i class="fas fa-user-tie nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Clients
    </a>
</li>

<li class="nav-item {{ Request::is('projects*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('projects.index') !!}">
        <i class="fa fa-folder-open nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Projects
    </a>
</li>

<li class="nav-item {{ Request::is('users*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('users.index') !!}">
        <i class="fa fa-users nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Users
    </a>
</li>

<li class="nav-item {{ Request::is('tasks*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('tasks.index') !!}">
        <i class="fa fa-tasks nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Tasks
    </a>
</li>

<li class="nav-item {{ Request::is('timeEntries*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('timeEntries.index') !!}">
        <i class="fas fa-user-clock nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Time Entries
    </a>
</li>

<li class="nav-item {{ Request::is('report*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! url('reports') !!}">
        <i class="fa fa-file nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Reports
    </a>
</li>
<li class="nav-item {{ Request::is('permissions*') ? 'active' : '' }}">
    <a class="nav-link" href="{!! url('permissions') !!}">
        <i class="fa fa-file nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Permissions
    </a>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="fa fa-cog nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Setting
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item {{ Request::is('tags*') ? 'active' : '' }}">
            <a class="nav-link" href="{!! route('tags.index') !!}">
                <i class="fa fa-tags nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Tags
            </a>
        </li>
        <li class="nav-item {{ Request::is('activityTypes*') ? 'active' : '' }}">
            <a class="nav-link" href="{!! route('activityTypes.index') !!}">
                <i class="fas fa-clipboard-list nav-icon" aria-hidden="true"></i>&nbsp;&nbsp;Activity Types
            </a>
        </li>
    </ul>
</li>
