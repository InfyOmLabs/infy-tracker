<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 *
 * User: Ajay Makwana
 * Email: ajay.makwana@infyom.com
 * Date: 5/8/2019
 * Time: 11:24 AM
 */

namespace App\Http\Controllers;

use App\Queries\ReportDataTable;
use App\Repositories\ReportRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends AppBaseController
{
    /**
     * @var ReportRepository
     */
    private $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * @param Request $request
     *
     * @return Factory|View
     * @throws Exception
     *
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new ReportDataTable())->get(
                $request->only('filter_task', 'filter_activity', 'filter_user', 'filter_project',
                    'filter_start_date', 'filter_end_date')
            ))->make(true);
        }

        $reportData = $this->reportRepository->getReportData();

        return view('reports.index')->with($reportData);
    }
}