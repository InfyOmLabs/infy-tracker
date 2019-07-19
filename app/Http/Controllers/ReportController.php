<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Report;
use App\Queries\ReportDataTable;
use App\Repositories\ClientRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ReportRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Arr;
use Auth;
use DataTables;
use Exception;
use Flash;
use Illuminate\Http\Request;
use Response;

class ReportController extends AppBaseController
{
    /** @var ReportRepository $reportRepository */
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
        TagRepository $tagRepository
    ) {
        $this->reportRepository = $reportRepo;
        $this->userRepo = $userRepository;
        $this->clientRepo = $clientRepository;
        $this->tagRepo = $tagRepository;
        $this->projectRepo = $projectRepository;
    }

    /**
     * Display a listing of the Reports.
     *
     * @param Request $request
     *
     * @throws Exception
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new ReportDataTable())->get())->make(true);
        }

        return view('reports.index');
    }

    /**
     * Show the form for creating a new Report.
     *
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
     *
     * @param CreateReportRequest $request
     *
     * @return Response
     */
    public function store(CreateReportRequest $request)
    {
        $input = $request->all();
        $input['owner_id'] = Auth::id();
        /** @var Report $report */
        $report = $this->reportRepository->create($input);
        $this->reportRepository->createReportFilter($input, $report);

        Flash::success('Report saved successfully.');

        return redirect(route('reports.index'));
    }

    /**
     * Display the specified Report.
     *
     * @param Report $report
     *
     * @return Response
     */
    public function show(Report $report)
    {
        $reports = $this->reportRepository->getReport($report);
        $duration = array_sum(Arr::pluck($reports, 'duration'));
        $totalHours = $this->reportRepository->getDurationTime($duration);
        $data = [
            'report'       => $report,
            'reports'      => $reports,
            'totalHours'   => $totalHours,
            'totalMinutes' => $duration,
        ];

        return view('reports.show')->with($data);
    }

    /**
     * Show the form for editing the specified Report.
     *
     * @param Report $report
     *
     * @return Response
     */
    public function edit(Report $report)
    {
        $id = $report->id;
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
     *
     * @param Report $report
     * @param UpdateReportRequest $request
     *
     * @throws Exception
     *
     * @return Response
     */
    public function update(Report $report, UpdateReportRequest $request)
    {
        $input = $request->all();
        $this->reportRepository->update($input, $report->id);
        $this->reportRepository->updateReportFilter($input, $report);
        Flash::success('Report updated successfully.');

        return redirect(route('reports.show', $report));
    }

    /**
     * Remove the specified Report from storage.
     *
     * @param Report $report
     *
     * @throws Exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Report $report)
    {
        $report->delete();
        $this->reportRepository->deleteFilter($report->id);

        Flash::success('Report deleted successfully.');
        if (request()->ajax()) {
            return $this->sendSuccess('Report deleted successfully.');
        }

        return redirect(route('reports.index'));
    }
}
