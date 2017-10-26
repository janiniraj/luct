<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Config;
use JWTAuth;
use JWTAuthException;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResultTrait;
use App\Models\Bookmark;

class BookmarkController extends Controller
{
    use ResultTrait;

    public function __construct()
    {
        $this->bookmark = new Bookmark();
    }

    /**
     * Create Bookmark
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_id'    => 'required',
            'bookmark'      => 'required'
        ]);

        // If validation fail, throw message
        if ($validator->fails())
        {
            $this->responseCode     = 500;
            $this->responseMessage  = "Invalid Input !";

            return $this->result([]);
        }

        $input      = $request->all();
        $user       = JWTAuth::parseToken()->authenticate();
dd($user);
        $userType   = $request->header('user-type') ? $request->header('user-type') : 'student';

        if($userType == 'member')
        {
            $userId = $user->userid;
        }
        else
        {
            $userId = $user->StudentID;
        }

        if($input['bookmark'])
        {
            $check  = $this->bookmark->where([
                'article_id'    => $input['article_id'],
                'user_id'       => $userId,
                'usertype'      => $userType
            ])->count();

            if($check > 0)
            {
                $this->responseCode     = 500;
                $this->responseMessage  = "Article Already Bookmarked";
            }
            else
            {
                $this->bookmark->insert([
                    'article_id'    => $input['article_id'],
                    'user_id'       => $userId,
                    'usertype'      => $userType
                ]);
                $this->responseMessage  = "Article Successfully Bookmarked";
            }
        }
        else
        {
            $check  = $this->bookmark->where([
                'article_id'    => $input['article_id'],
                'user_id'       => $userId,
                'usertype'      => $userType
            ])->count();

            if($check > 0)
            {
                $this->bookmark->where([
                    'article_id'    => $input['article_id'],
                    'user_id'       => $userId,
                    'usertype'      => $userType
                ])->delete();

                $this->responseMessage  = "Article Successfully Removed from Bookmark List";
            }
            else
            {
                $this->responseCode     = 500;
                $this->responseMessage  = "No such article in bookmark list.";
            }
        }

        return $this->result([]);
    }

    /**
     * Remove Bookmark
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_id' => 'required'
        ]);

        // If validation fail, throw message
        if ($validator->fails())
        {
            $this->responseCode     = 500;
            $this->responseMessage  = "Invalid Input !";

            return $this->result([]);
        }

        $input  = $request->all();
        $user   = JWTAuth::parseToken()->authenticate();
        $check  = $this->bookmark->where([
                    'article_id'    => $input['article_id'],
                    'user_id'       => $user['userid'],
                    'usertype'      => $user['usertype']
                ])->count();

        if($check > 0)
        {
            $this->bookmark->where([
                'article_id'    => $input['article_id'],
                'user_id'       => $user['userid'],
                'usertype'      => $user['usertype']
            ])->delete();

            $this->responseMessage  = "Article Successfully Removed from Bookmark List";
        }
        else
        {
            $this->responseCode     = 500;
            $this->responseMessage  = "No such article in bookmark list.";
        }

        return $this->result([]);
    }
}