<?php namespace App\Http\Transformers;
use App\Http\Transformers\Transformer;
use URL;

/**
 * Class EventTransformer
 * @package App\Http\Transformers
 */
class EventTransformer extends Transformer
{
    /**
     * Transform Data
     *
     * @param $data
     * @return array
     */
    public function transform($data)
    {
        return [
            'id'                => $this->nulltoBlank($data['id']),
            'name'              => $this->nulltoBlank($data['name']),
            'date'              => $this->nulltoBlank($data['date']),
            'thumbnail'         => $this->nulltoBlank($data['thumbnail']),
            'thumbnail_small'   => $this->nulltoBlank($data['thumbnail_small']),
            'description'       => $this->nulltoBlank($data['description']),
            'share'             => $this->nulltoBlank($data['share']),
            'start_time'        => $this->nulltoBlank($data['start_time']),
            'end_time'          => $this->nulltoBlank($data['end_time']),
            'events_venue'      => $this->nulltoBlank($data['events_venue']),
            'bookmarked'        => $data['bookmarked'] ? 1 : 0
        ];
    }

    public function transformSingle($data)
    {

        return [
            'title'             => $this->nulltoBlank($data['title']),
            'start_date'        => $this->nulltoBlank($data['start_date']),
            'end_date'          => $this->nulltoBlank($data['end_date']),
            'venue'             => $this->nulltoBlank($data['venue']),
            'start_time'        => $this->nulltoBlank($data['start_time']),
            'end_time'          => $this->nulltoBlank($data['end_time']),
            'share'             => $this->nulltoBlank($data['share']),
            'body'              => $this->nulltoBlank($data['body']),
            'image'             => $this->nulltoBlank($data['picture']['@attributes']['image']),
            'gallery'           => $this->getGalleryPictures($data),
            'bookmarked'        => $data['bookmarked'] ? 1 : 0
        ];
    }

    public function getGalleryPictures($data)
    {
        $imageList = [];

        if(isset($data['picture']['gallerypic']) && !empty($data['picture']['gallerypic']))
        {
            foreach($data['picture']['gallerypic'] as $singlePictureArray)
            {
                $imageList[] = $singlePictureArray['@attributes']['url'];
            }
        }

        return $imageList;
    }
}