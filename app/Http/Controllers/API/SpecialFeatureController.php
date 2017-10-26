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
            $user       = JWTAuth::parseToken()->authenticate();
            $userType   = $request->header('user-type') ? $request->header('user-type') : 'student';

            if($userType == 'member')
            {
                $userId = $user->userid;
            }
            else
            {
                $userId = $user->StudentID;
            }
        }

        $specialFeatures   = [];

        if(isset($mainArray['article']) && !empty($mainArray['article']))
        {
            foreach($mainArray['article'] as $key => $value)
            {
                $value['@attributes']['bookmarked'] = 0;

                if(isset($user) && !empty($user))
                {
                    if($this->bookmark->checkArticleBookmarked($value['@attributes']['id'], $userId, $userType))
                    {
                        $value['@attributes']['bookmarked'] = 1;
                    }
                }

                $specialFeatures[] = $value['@attributes'];
            }
        }
        $data = $this->transformer->transformCollection($specialFeatures);

        if(!isset($mainArray['pagging']['page']['@attributes']))
        {
            $mainArray['pagging']['page']['@attributes']['current_page']    = 1;
            $mainArray['pagging']['page']['@attributes']['total_pages']     = 1;
        }

        return $this->respondWithPagination($data, $mainArray['pagging']['page']['@attributes']);
    }


    public function show($specialFeatureId, Request $request)
    {
        $url        = 'https://www.limkokwing.net/json/sf_content?id='.$specialFeatureId;
        $mainArray  = $this->getResponseFromUrl($url);

        if($mainArray === false)
        {
            return $this->respondInternalError("No Such Special Feature Found.");
        }
        else
        {
            if(JWTAuth::getToken())
            {
                $user       = JWTAuth::parseToken()->authenticate();
                $userType   = $request->header('user-type') ? $request->header('user-type') : 'student';

                if($userType == 'member')
                {
                    $userId = $user->userid;
                }
                else
                {
                    $userId = $user->StudentID;
                }
                $mainArray['bookmarked'] = $this->bookmark->checkArticleBookmarked($specialFeatureId, $userId, $userType);
            }
            else
            {
                $mainArray['bookmarked'] = 0;
            }

            $data = $this->transformer->transformSingle($mainArray);

            return $this->ApiSuccessResponse($data);
        }
    }
}