<?php

namespace App\Repositories;

use App\Models\Tag;

/**
 * Class TagRepository.
 *
 * @version May 3, 2019, 4:33 am UTC
 */
class TagRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
    ];

    /**
     * Return searchable fields.
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model.
     **/
    public function model()
    {
        return Tag::class;
    }

    /**
     * @param array $input
     *
     * @return bool
     */
    public function store($input)
    {
        if (isset($input['bulk_tags']) && $input['bulk_tags'] == true) {
            $bulkTags = explode_trim_remove_empty_values_from_array($input['name'], ',');
            foreach ($bulkTags as $tag) {
                $isExist = Tag::whereRaw('lower(name) = ?', strtolower($tag))->exists();
                if ($isExist) {
                    continue;
                }
                Tag::create([
                    'name'       => $tag,
                    'created_by' => getLoggedInUserId(),
                ]);
            }

            return true;
        }

        $input['created_by'] = getLoggedInUserId();
        Tag::create($input);
    }

    /**
     * @return mixed
     */
    public function getTagList()
    {
        return Tag::orderBy('name')->pluck('name', 'id');
    }
}
