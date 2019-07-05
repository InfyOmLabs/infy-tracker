<?php

namespace App\Http\Controllers;

use App\Repositories\DashboardRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class HomeController extends AppBaseController
{
    /** @var  DashboardRepository $dashboardRepo */
    private $dashboardRepo;
    private $userRepository;

    /**
     * HomeController constructor.
     * @param DashboardRepository $dashboardRepository
     * @param UserRepository $userRepository
     */
    public function __construct(DashboardRepository $dashboardRepository,UserRepository $userRepository)
    {
        $this->middleware('auth');
        $this->dashboardRepo = $dashboardRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Show the application dashboard.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->userRepository->getUserList();
        return view('dashboard.index', compact('users'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function workReport(Request $request)
    {
        $data = $this->dashboardRepo->getWorkReport($request->all());

        return $this->sendResponse($data, 'Work Report retrieved successfully.');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function developerWorkReport(Request $request)
    {
        $data = $this->dashboardRepo->getDeveloperWorkReport($request->all());

        return $this->sendResponse($data, 'Developer Work Report retrieved successfully.');
    }
}
