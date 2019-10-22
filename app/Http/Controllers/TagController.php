<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use App\Queries\TagDataTable;
use App\Repositories\TagRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class TagController.
 */
class TagController extends AppBaseController
{
    /** @var TagRepository */
    private $tagRepository;

    /**
     * TagController constructor.
     *
     * @param TagRepository $tagRepo
     */
    public function __construct(TagRepository $tagRepo)
    {
        $this->tagRepository = $tagRepo;
    }

    /**
     * @param Request $request
     *
     * @throws Exception
     *
     * @return Factory|View
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
     * @param Tag $tag
     *
     * @return JsonResponse
     */
    public function edit(Tag $tag)
    {
        return $this->sendResponse($tag, 'Tag retrieved successfully.');
    }

    /**
     * Update the specified Tag in storage.
     *
     * @param Tag              $tag
     * @param UpdateTagRequest $request
     *
     * @return JsonResponse
     */
    public function update(Tag $tag, UpdateTagRequest $request)
    {
        $this->tagRepository->update($request->all(), $tag->id);

        return $this->sendSuccess('Tag updated successfully.');
    }

    /**
     * Remove the specified Tag from storage.
     *
     * @param Tag $tag
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy(Tag $tag)
    {
        $tag->deleted_by = getLoggedInUserId();
        $tag->save();
        $tag->delete();

        return $this->sendSuccess('Tag deleted successfully.');
    }
}
