<?php namespace App\Http\Transformers;

use App\Http\Transformers\Transformer;
use URL;

/**
 * Class HighLightTransformer
 *
 * @package App\Http\Transformers
 */
class HighLightTransformer extends Transformer
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
            'type'              => $this->nulltoBlank($data['article_type']),
            'bookmarked'        => $data['bookmarked'] ? 1 : 0
        ];
    }
}