<?php

namespace App\Http\Controllers;

use App\Repositories\DashboardRepository;
use App\Repositories\UserRepository;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class HomeController.
 */
class HomeController extends AppBaseController
{
    /** @var DashboardRepository */
    private $dashboardRepo;

    /** @var UserRepository */
    private $userRepository;

    /**
     * HomeController constructor.
     *
     * @param DashboardRepository $dashboardRepository
     * @param UserRepository      $userRepository
     */
    public function __construct(DashboardRepository $dashboardRepository, UserRepository $userRepository)
    {
        $this->middleware('auth');
        $this->dashboardRepo = $dashboardRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $users = $this->userRepository->getUserList();

        return view('dashboard.index', compact('users'));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function workReport(Request $request)
    {
        if (!authUserHasPermission('manage_users')) {
            $request->request->set('user_id', Auth::id());
        }
        $data = $this->dashboardRepo->getWorkReport($request->all());

        return $this->sendResponse($data, 'Custom Report retrieved successfully.');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function developerWorkReport(Request $request)
    {
        $data = $this->dashboardRepo->getDeveloperWorkReport($request->all());

        return $this->sendResponse($data, 'Daily Work Report retrieved successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function userOpenTasks()
    {
        $data = $this->dashboardRepo->getUserOpenTasks();

        return $this->sendResponse($data, 'Open Task retrieved successfully.');
    }
}
