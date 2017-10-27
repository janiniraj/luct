<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Config;
use JWTAuth;
use JWTAuthException;
use App\Http\Transformers\HighLightTransformer;
use App\Models\Bookmark;

/**
 * Class HighLightController
 *
 * @package App\Http\Controllers\API
 */
class HighLightController extends Controller
{
    /**
     * HighLightController constructor.
     *
     * @param HighLightTransformer $highLightTransformer
     */
    public function __construct(HighLightTransformer $highLightTransformer)
    {
        $this->transformer  = $highLightTransformer;
        $this->bookmark     = new Bookmark();
    }

    /**
     * Highlight Listing
     *
     * @param Request $request
     * @return \App\Http\Controllers\mix
     */
    public function index(Request $request)
    {
        $url    = 'https://www.limkokwing.net/json/highlights';
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

        $articles   = [];

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

                $articles[] = $value['@attributes'];
            }
        }
        $data = $this->transformer->transformCollection($articles);

        if(!isset($mainArray['pagging']['page']['@attributes']))
        {
            $mainArray['pagging']['page']['@attributes']['current_page']    = 1;
            $mainArray['pagging']['page']['@attributes']['total_pages']     = 1;
        }

        return $this->respondWithPagination($data, $mainArray['pagging']['page']['@attributes']);
    }
}