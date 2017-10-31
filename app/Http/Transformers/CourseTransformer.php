<?php namespace App\Http\Transformers;

use App\Http\Transformers\Transformer;
use URL;

/**
 * Class CourseTransformer
 * @package App\Http\Transformers
 */
class CourseTransformer extends Transformer
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
            'id'                => $this->nulltoBlank($data['@attributes']['id']),
            'name'              => $this->nulltoBlank($data['@attributes']['name']),
            'campus'            => isset($data['campus']['@attributes']) ? $this->getCampusData($data['campus']) : $this->transformCollection($data['campus'], 'getCampusData')
        ];
    }

    /**
     * Get Campus Data
     *
     * @param $data
     * @return array
     */
    public function getCampusData($data)
    {
        if(!isset($data['level']) && isset($data['level_content']))
        {
            $data['level']['@attributes'] = [
                'id'    => '',
                'name'  => ''
            ];
            $data['level']['faculty'] = [
                '@attributes' => [
                    'id'    => '',
                    'name'  => ''
                ],
                'level_content' => $data['level_content']
            ];
        }
        return[
            'id'                => $this->nulltoBlank($data['@attributes']['id']),
            'name'              => $this->nulltoBlank($data['@attributes']['name']),
            'level'             => isset($data['level']['@attributes']) ? $this->getLevelData($data['level']) : $this->transformCollection($data['level'], 'getLevelData')
        ];
    }

    /**
     * Get Level Data
     *
     * @param $data
     * @return array
     */
    public function getLevelData($data)
    {
        if(!isset($data['faculty']) && isset($data['level_content']))
        {
            $data['faculty'] = [
                '@attributes' => [
                    'id'    => '',
                    'name'  => ''
                ],
                'level_content' => $data['level_content']
            ];
        }
        return [
            'id'                => $this->nulltoBlank($data['@attributes']['id']),
            'name'              => $this->nulltoBlank($data['@attributes']['name']),
            'faculty'             => isset($data['faculty']['@attributes']) ? $this->getFacultyData($data['faculty']) : $this->transformCollection($data['faculty'], 'getFacultyData')
        ];
    }

    /**
     * Get Faculty Data
     *
     * @param $data
     * @return array
     */
    public function getFacultyData($data)
    {
        return [
            'id'                => $this->nulltoBlank($data['@attributes']['id']),
            'name'              => $this->nulltoBlank($data['@attributes']['name']),
            'level_content'     => isset($data['level_content']['@attributes']) ? $this->getLevelContentData($data['level_content']) : $this->transformCollection($data['level_content'], 'getLevelContentData')
        ];
    }

    /**
     * Get Level Content Data
     *
     * @param $data
     * @return array
     */
    public function getLevelContentData($data)
    {
        return [
            'id'                => $this->nulltoBlank($data['@attributes']['id']),
            'name'              => $this->nulltoBlank($data['@attributes']['name']),
            'url'               => $this->nulltoBlank($data['@attributes']['url']),
        ];
    }
}