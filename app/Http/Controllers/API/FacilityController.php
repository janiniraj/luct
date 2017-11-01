<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Config;
use JWTAuth;
use JWTAuthException;
use App\Http\Transformers\FacilityTransformer;

/**
 * Class FacilityController
 * @package App\Http\Controllers\API
 */
class FacilityController extends Controller
{
    /**
     * FacilityController constructor.
     * @param FacilityTransformer $facilityTransformer
     */
    public function __construct(FacilityTransformer $facilityTransformer)
    {
        $this->transformer  = $facilityTransformer;
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\mix
     */
    public function index(Request $request)
    {
        $url        = 'https://www.limkokwing.net/json/facilities';
        $mainArray  = $this->getResponseFromUrl($url);

        $data = $this->transformer->transformCollection($mainArray['country']);

        return $this->respond($data);
    }


    public function show($articleId, Request $request)
    {
        $url        = 'https://www.limkokwing.net/json/article_content?id='.$articleId;
        $mainArray  = $this->getResponseFromUrl($url);

        if($mainArray === false)
        {
            return $this->respondInternalError("No Such Article Found.");
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
                $mainArray['bookmarked'] = $this->bookmark->checkArticleBookmarked($articleId, $userId, $userType);
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