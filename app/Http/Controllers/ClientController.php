<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Queries\ClientDataTable;
use App\Repositories\ClientRepository;
use App\Repositories\ProjectRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class ClientController.
 */
class ClientController extends AppBaseController
{
    /** @var ClientRepository */
    private $clientRepository;

    /**
     * ClientController constructor.
     *
     * @param ClientRepository $clientRepo
     */
    public function __construct(ClientRepository $clientRepo)
    {
        $this->clientRepository = $clientRepo;
    }

    /**
     * Display a listing of the Client.
     *
     * @param Request $request
     *
     * @throws Exception
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new ClientDataTable())->get())->make(true);
        }

        return view('clients.index');
    }

    /**
     * Store a newly created Client in storage.
     *
     * @param CreateClientRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateClientRequest $request)
    {
        $input = $request->all();
        $input['created_by'] = getLoggedInUserId();

        $this->clientRepository->create($this->fill($input));

        return $this->sendSuccess('Client created successfully.');
    }

    /**
     * @param array $input
     *
     * @return mixed
     */
    public function fill($input)
    {
        $input['email'] = is_null($input['email']) ? '' : $input['email'];
        $input['website'] = is_null($input['website']) ? '' : $input['website'];

        return $input;
    }

    /**
     * Show the form for editing the specified Client.
     *
     * @param Client $client
     *
     * @return JsonResponse
     */
    public function edit(Client $client)
    {
        return $this->sendResponse($client, 'Client retrieved successfully.');
    }

    /**
     * Update the specified Client in storage.
     *
     * @param Client              $client
     * @param UpdateClientRequest $request
     *
     * @return JsonResponse
     */
    public function update(Client $client, UpdateClientRequest $request)
    {
        $this->clientRepository->update($this->fill($request->all()), $client->id);

        return $this->sendSuccess('Client updated successfully.');
    }

    /**
     * Remove the specified Client from storage.
     *
     * @param Client $client
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy(Client $client)
    {
        $this->clientRepository->delete($client->id);

        return $this->sendSuccess('Client deleted successfully.');
    }

    /**
     * @param Request           $request
     * @param ProjectRepository $projectRepository
     *
     * @return JsonResponse
     */
    public function projects(Request $request, ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->getProjectsList($request->get('client_id', null));

        return $this->sendResponse($projects, 'Projects retrieved successfully.');
    }
}
