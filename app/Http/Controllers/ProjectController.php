<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Queries\ProjectDataTable;
use App\Repositories\ClientRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use DataTables;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends AppBaseController
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var ClientRepository */
    private $clientRepository;

    /** @var UserRepository */
    private $userRepository;

    /**
     * ProjectController constructor.
     *
     * @param ProjectRepository $projectRepo
     * @param ClientRepository  $clientRepo
     * @param UserRepository    $userRepository
     */
    public function __construct(
        ProjectRepository $projectRepo,
        ClientRepository $clientRepo,
        UserRepository $userRepository
    ) {
        $this->projectRepository = $projectRepo;
        $this->clientRepository = $clientRepo;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the Project.
     *
     * @param Request $request
     *
     * @throws Exception
     *
     * @return View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new ProjectDataTable())->get(
                $request->only('filter_client'))
            )->make(true);
        }

        $clients = $this->clientRepository->getClientList();
        $users = $this->userRepository->getUserList();

        return view('projects.index', compact('clients', 'users'));
    }

    /**
     * Store a newly created Project in storage.
     *
     * @param CreateProjectRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateProjectRequest $request)
    {
        $input = $request->all();
        $input['created_by'] = getLoggedInUserId();
        $input['description'] = is_null($input['description']) ? '' : $input['description'];

        /** @var Project $project */
        $project = $this->projectRepository->create($input);
        $project->users()->sync($input['user_ids']);

        return $this->sendSuccess('Project created successfully.');
    }

    /**
     * Show the form for editing the specified Project.
     *
     * @param Project $project
     *
     * @return JsonResponse|RedirectResponse
     */
    public function edit(Project $project)
    {
        $project->users;
        $users = $project->users->pluck('id')->toArray();

        return $this->sendResponse(['project' => $project, 'users' => $users], 'Project retrieved successfully.');
    }

    /**
     * Update the specified Client in storage.
     *
     * @param Project              $project
     * @param UpdateProjectRequest $request
     *
     * @return JsonResponse|RedirectResponse
     */
    public function update(Project $project, UpdateProjectRequest $request)
    {
        $input = $request->all();
        $input['description'] = is_null($input['description']) ? '' : $input['description'];

        /** @var Project $project */
        $project = $this->projectRepository->update($input, $project->id);
        $project->users()->sync($input['user_ids']);

        return $this->sendSuccess('Project updated successfully.');
    }

    /**
     * Remove the specified Project from storage.
     *
     * @param Project $project
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return $this->sendSuccess('Project deleted successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function getMyProjects()
    {
        $projects = $this->projectRepository->getMyProjects();

        return $this->sendResponse($projects, 'Project Retrieved successfully.');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function users(Request $request)
    {
        $projectIds = $request->get('projectIds', null);

        $projectIdsArr = [];
        if (!is_null($projectIds)) {
            $projectIdsArr = explode(',', $projectIds);
        }
        $users = $this->userRepository->getUserList($projectIdsArr);

        return $this->sendResponse($users, 'Users Retrieved successfully.');
    }
}
