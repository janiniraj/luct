<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Config;
use JWTAuth;
use JWTAuthException;
use App\Http\Transformers\CourseTransformer;
use App\Models\Bookmark;

/**
 * Class CourseController
 * @package App\Http\Controllers\API
 */
class CourseController extends Controller
{
    /**
     * CourseController constructor.
     * @param CourseTransformer $courseTransformer
     */
    public function __construct(CourseTransformer $courseTransformer)
    {
        $this->transformer  = $courseTransformer;
        $this->bookmark     = new Bookmark();
    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\mix
     */
    public function index(Request $request)
    {
        $url        = 'https://www.limkokwing.net/json/courses';
        $mainArray  = $this->getResponseFromUrl($url);

        $data = $this->transformer->transformCollection($mainArray['country']);

        return $this->respond($data);
    }

    public function show($courseId, Request $request)
    {
        $url        = 'https://www.limkokwing.net/json/courses_details?id='.$courseId;
        $mainArray  = $this->getResponseFromUrl($url);

        if($mainArray === false)
        {
            return $this->respondInternalError("No Such Course Found.");
        }
        else
        {

            $data = $this->transformer->transformSingle($mainArray);

            return $this->ApiSuccessResponse($data);
        }
    }
}