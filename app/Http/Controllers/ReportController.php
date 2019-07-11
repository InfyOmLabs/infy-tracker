<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Repositories\ClientRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ReportRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Auth;
use Flash;
use Illuminate\Http\Request;
use Response;

class ReportController extends AppBaseController
{
    /** @var  ReportRepository $reportRepository */
    private $reportRepository;
    /** @var UserRepository $userRepo */
    private $userRepo;
    /** @var TagRepository $tagRepo */
    private $tagRepo;
    /** @var ClientRepository $clientRepo */
    private $clientRepo;
    /** @var ProjectRepository $projectRepository */
    private $projectRepo;

    public function __construct(
        ReportRepository $reportRepo,
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        ClientRepository $clientRepository,
        TagRepository $tagRepository)
    {
        $this->reportRepository = $reportRepo;
        $this->userRepo = $userRepository;
        $this->clientRepo = $clientRepository;
        $this->tagRepo = $tagRepository;
        $this->projectRepo = $projectRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $reports = $this->reportRepository->all();

        return view('reports.index')->with('reports', $reports);
    }

    /**
     * Show the form for creating a new Report.
     * @return Response
     */
    public function create()
    {
        $data['tags'] = $this->tagRepo->getTagList();
        $data['users'] = $this->userRepo->getUserList();
        $data['clients'] = $this->clientRepo->getClientList();
        $data['projects'] = $this->projectRepo->getProjectsList();
        return view('reports.create', $data);
    }

    /**
     * Store a newly created Report in storage.
     * @param CreateReportRequest $request
     * @return Response
     */
    public function store(CreateReportRequest $request)
    {
        $input = $request->all();
        $input['owner_id'] = Auth::user()->id;
        $report = $this->reportRepository->create($input);
        $this->reportRepository->createReportFilter($input, $report);

        Flash::success('Report saved successfully.');

        return redirect(route('reports.index'));
    }

    /**
     * Display the specified Report.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $report = $this->reportRepository->find($id);

        if (empty($report)) {
            Flash::error('Report not found.');
            return redirect(route('reports.index'));
        }

        return view('reports.show')->with('report', $report);
    }

    /**
     * Show the form for editing the specified Report.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $report = $this->reportRepository->find($id);

        if (empty($report)) {
            Flash::error('Report not found.');
            return redirect(route('reports.index'));
        }
        $data['report'] = $report;
        $data['projectIds'] = $this->reportRepository->getProjectIds($id);
        $data['tagIds'] = $this->reportRepository->getTagIds($id);
        $data['userIds'] = $this->reportRepository->getUserIds($id);
        $data['clientId'] = $this->reportRepository->getClientId($id);
        $data['projects'] = $this->projectRepo->getProjectsList($data['clientId']);
        $data['users'] = $this->userRepo->getUserList($data['projectIds']);
        $data['clients'] = $this->clientRepo->getClientList();
        $data['tags'] = $this->tagRepo->getTagList();
        return view('reports.edit')->with($data);
    }

    /**
     * Update the specified Report in storage.
     * @param int $id
     * @param UpdateReportRequest $request
     * @return Response
     */
    public function update($id, UpdateReportRequest $request)
    {
        $report = $this->reportRepository->find($id);

        if (empty($report)) {
            Flash::error('Report not found.');
            return redirect(route('reports.index'));
        }
        $input = $request->all();
        $this->reportRepository->update($input, $id);
        $this->reportRepository->updateReportFilter($input, $report);
        Flash::success('Report updated successfully.');

        return redirect(route('reports.index'));
    }

    /**
     * Remove the specified Report from storage.
     * @param int $id
     * @throws \Exception
     * @return Response
     */
    public function destroy($id)
    {
        $report = $this->reportRepository->find($id);

        if (empty($report)) {
            Flash::error('Report not found.');

            return redirect(route('reports.index'));
        }

        $this->reportRepository->delete($id);
        $this->reportRepository->deleteFilter($id);

        Flash::success('Report deleted successfully.');

        return redirect(route('reports.index'));
    }
}
