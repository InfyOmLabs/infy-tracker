<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Queries\ClientDataTable;
use App\Repositories\ClientRepository;
use DataTables;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends AppBaseController
{
    /** @var ClientRepository */
    private $clientRepository;

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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

        return $this->sendSuccess('Review created successfully.');
    }

    /**
     * @param $input
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
     * @param int $id
     *
     * @return JsonResponse
     */
    public function edit($id)
    {
        $client = $this->clientRepository->findOrFail($id);

        return $this->sendResponse($client, 'Client retrieved successfully.');
    }

    /**
     * Update the specified Client in storage.
     *
     * @param int                 $id
     * @param UpdateClientRequest $request
     *
     * @return JsonResponse
     */
    public function update($id, UpdateClientRequest $request)
    {
        $this->clientRepository->findOrFail($id);
        $this->clientRepository->update($this->fill($request->all()), $id);

        return $this->sendSuccess('Client updated successfully.');
    }

    /**
     * Remove the specified Client from storage.
     *
     * @param int $id
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->clientRepository->findOrFail($id);
        $this->clientRepository->delete($id);

        return $this->sendSuccess('Client deleted successfully.');
    }
}
