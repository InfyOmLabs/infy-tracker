<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\DashboardRepository;
use Illuminate\Http\Request;

class HomeController extends AppBaseController
{
    /** @var  DashboardRepository $dashboardRepo */
    private $dashboardRepo;

    /**
     * HomeController constructor.
     * @param DashboardRepository $dashboardRepository
     */
    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->middleware('auth');
        $this->dashboardRepo = $dashboardRepository;
    }

    /**
     * Show the application dashboard.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::pluck('name', 'id');
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
