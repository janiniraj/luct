<?php namespace App\Http\Transformers;
use App\Http\Transformers\Transformer;
use URL;

/**
 * Class ArticleTransformer
 * @package App\Http\Transformers
 */
class ArticleTransformer extends Transformer
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
            'name'              => $this->nulltoBlank($data['name']),
            'url'               => $this->nulltoBlank($data['url']),
            'date'              => $this->nulltoBlank($data['date']),
            'thumbnail'         => $this->nulltoBlank($data['thumbnail']),
            'thumbnail_small'   => $this->nulltoBlank($data['thumbnail_small']),
            'description'       => $this->nulltoBlank($data['description']),
            'share'             => $this->nulltoBlank($data['share'])
        ];
    }
}