<?php namespace App\Http\Transformers;
use App\Http\Transformers\Transformer;
use URL;

/**
 * Class FacilityTransformer
 * @package App\Http\Transformers
 */
class FacilityTransformer extends Transformer
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
            'country_name'      => $this->nulltoBlank($data['@attributes']['name']),
            'facility'          => isset($data['facility']) ? $this->transformCollection($data['facility'], 'getFacilityData') : []
        ];
    }

    public function getFacilityData($data)
    {
        return [
            'title'         => $this->nulltoBlank($data['@attributes']['title']),
            'image'         => $this->nulltoBlank($data['@attributes']['image']),
            'entry_id'      => $this->nulltoBlank($data['@attributes']['entry_id']),
            'body'          => $this->nulltoBlank($data['body']),
            'gallery'       => $this->getGalleryPictures($data)
        ];
    }

    public function getGalleryPictures($data)
    {
        $imageList = [];

        if(isset($data['gallery']['images']) && !empty($data['gallery']['images']))
        {
            foreach($data['gallery']['images'] as $singlePictureArray)
            {
                if(isset($singlePictureArray['@attributes']))
                {
                    $imageList[] = $singlePictureArray['@attributes']['src'];
                }
                else if(isset($singlePictureArray['src']))
                {
                    $imageList[] = $singlePictureArray['src'];
                }
                else
                {
                    continue;
                }
            }
        }

        return $imageList;
    }
}