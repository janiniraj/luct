<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Config;
use JWTAuth;
use JWTAuthException;
use App\Http\Transformers\EventTransformer;

/**
 * Class EventController
 * @package App\Http\Controllers\API
 */
class EventController extends Controller
{
    /**
     * EventController constructor.
     * @param EventTransformer $eventTransformer
     */
    public function __construct(EventTransformer $eventTransformer)
    {
        $this->transformer = $eventTransformer;
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\mix
     */
    public function index(Request $request)
    {
        $url    = 'https://www.limkokwing.net/json/events';
        $input  = $request->all();
        $page   = 1;

        if(isset($input['page']) && $input['page'])
        {
            $page = $input['page'];
        }

        $url        .= "?page=".$page;
        $mainArray  = $this->getResponseFromUrl($url);
        $events   = [];

        if(isset($mainArray['event']) && !empty($mainArray['event']))
        {
            foreach($mainArray['event'] as $key => $value)
            {
                $events[] = $value['@attributes'];
            }
        }
        $data = $this->transformer->transformCollection($events);

        if(isset($mainArray['pagging']['page']['@attributes']) )
        {
            return $this->respondWithPagination($data, $mainArray['pagging']['page']['@attributes']);
        }
        else
        {
            return $this->ApiSuccessResponse($data);
        }
    }
}