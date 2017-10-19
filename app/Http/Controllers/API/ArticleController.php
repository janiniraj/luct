<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Config;
use JWTAuth;
use JWTAuthException;
use Ixudra\Curl\Facades\Curl;
use App\Http\Transformers\ArticleTransformer;

/**
 * Class ArticleController
 * @package App\Http\Controllers\API
 */
class ArticleController extends Controller
{
    /**
     * ArticleController constructor.
     * @param ArticleTransformer $articleTransformer
     */
    public function __construct(ArticleTransformer $articleTransformer)
    {
        $this->transformer = $articleTransformer;
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\mix
     */
    public function index(Request $request)
    {
        $url    = 'https://www.limkokwing.net/json/articles';
        $input  = $request->all();
        $page   = 1;

        if(isset($input['page']) && $input['page'])
        {
            $page = $input['page'];
        }

        $url        .= "?page=".$page;
        $response   = Curl::to($url)->get();
        $xml        = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        $mainArray  = json_decode(json_encode((array)$xml), TRUE);
        $articles   = [];

        if(isset($mainArray['article']) && !empty($mainArray['article']))
        {
            foreach($mainArray['article'] as $key => $value)
            {
                $articles[] = $value['@attributes'];
            }
        }
        $data = $this->transformer->transformCollection($articles);

        if(isset($mainArray['pagging']['page']['@attributes']) )
        {
            return $this->respondWithPagination($data, $mainArray['pagging']['page']['@attributes']);
        }
        else
        {
            return $this->ApiSuccessResponse($data);
        }
    }


    public function show($articleId, Request $request)
    {

    }
}