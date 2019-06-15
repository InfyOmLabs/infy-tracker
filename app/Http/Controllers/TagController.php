<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Queries\TagDataTable;
use App\Repositories\TagRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TagController extends AppBaseController
{
    /** @var  TagRepository */
    private $tagRepository;

    /**
     * TagController constructor.
     * @param TagRepository $tagRepo
     */
    public function __construct(TagRepository $tagRepo)
    {
        $this->tagRepository = $tagRepo;
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
            return DataTables::of((new TagDataTable())->get($request->only(['name'])))->make(true);
        }

        return view('tags.index');
    }

    /**
     * Store a newly created Tag in storage.
     *
     * @param CreateTagRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateTagRequest $request)
    {
        $input = $request->all();
        $this->tagRepository->store($input);

        return $this->sendSuccess('Tag created successfully.');
    }

    /**
     * Show the form for editing the specified Tag.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function edit($id)
    {
        $tag = $this->tagRepository->findOrFail($id);

        return $this->sendResponse($tag, 'Tag retrieved successfully.');
    }

    /**
     * Update the specified Tag in storage.
     *
     * @param int $id
     * @param UpdateTagRequest $request
     *
     * @return JsonResponse
     */
    public function update($id, UpdateTagRequest $request)
    {
        $this->tagRepository->findOrFail($id);

        $this->tagRepository->update($request->all(), $id);

        return $this->sendSuccess('Tag updated successfully.');
    }

    /**
     * Remove the specified Tag from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        $this->tagRepository->findOrFail($id);

        $this->tagRepository->delete($id);

        return $this->sendSuccess('Tag deleted successfully.');
    }
}
