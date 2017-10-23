<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Config;
use JWTAuth;
use JWTAuthException;
use App\Http\Transformers\SpecialFeatureTransformer;
use App\Models\Bookmark;

/**
 * Class SpecialFeatureController
 * @package App\Http\Controllers\API
 */
class SpecialFeatureController extends Controller
{
    /**
     * SpecialFeatureController constructor.
     * @param SpecialFeatureTransformer $specialFeatureTransformer
     */
    public function __construct(SpecialFeatureTransformer $specialFeatureTransformer)
    {
        $this->transformer  = $specialFeatureTransformer;
        $this->bookmark     = new Bookmark();
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\mix
     */
    public function index(Request $request)
    {
        $url    = 'https://www.limkokwing.net/json/special_features';
        $input  = $request->all();
        $page   = 1;

        if(isset($input['page']) && $input['page'])
        {
            $page = $input['page'];
        }

        $url        .= "?page=".$page;
        $mainArray  = $this->getResponseFromUrl($url);

        if(JWTAuth::getToken())
        {
            $user = JWTAuth::parseToken()->authenticate();
        }

        $specialFeatures   = [];

        if(isset($mainArray['article']) && !empty($mainArray['article']))
        {
            foreach($mainArray['article'] as $key => $value)
            {
                if(isset($user) && !empty($user))
                {
                    if($this->bookmark->checkArticleBookmarked($value['@attributes']['id'], $user['userid'], $user['usertype']))
                    {
                        $value['@attributes']['bookmarked'] = 1;
                    }
                }

                $specialFeatures[] = $value['@attributes'];
            }
        }
        $data = $this->transformer->transformCollection($specialFeatures);

        if(isset($mainArray['pagging']['page']['@attributes']) )
        {
            return $this->respondWithPagination($data, $mainArray['pagging']['page']['@attributes']);
        }
        else
        {
            return $this->ApiSuccessResponse($data);
        }
    }


    public function show($specialFeatureId, Request $request)
    {
        $url        = 'https://www.limkokwing.net/json/sf_content?id='.$specialFeatureId;
        $mainArray  = $this->getResponseFromUrl($url);

        if(JWTAuth::getToken())
        {
            $user = JWTAuth::parseToken()->authenticate();
            $mainArray['bookmarked'] = $this->bookmark->checkArticleBookmarked($specialFeatureId, $user['userid'], $user['usertype']);
        }
        else
        {
            $mainArray['bookmarked'] = 0;
        }

        if($mainArray === false)
        {
            return $this->respondInternalError("No Such Special Feature Found.");
        }
        else
        {
            $data = $this->transformer->transformSingle($mainArray);

            return $this->ApiSuccessResponse($data);
        }
    }
}