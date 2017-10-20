<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Config;
use JWTAuth;
use JWTAuthException;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResultTrait;
use App\Models\UserMeta;
use App\Models\LoginAttempt;
use App\Models\Member;
use App\Models\Staff;
use App\Models\Student;
use App\Http\Transformers\UserTransformer;

class ApiController extends Controller
{
    public function __construct(UserTransformer $userTransformer)
    {
        $this->userTransformer  = $userTransformer;
        $this->userMeta         = new UserMeta();
        $this->loginAttempt     = new LoginAttempt();
        $this->member           = new Member();
        $this->staff            = new Staff();
        $this->student          = new Student();
    }

    /**
     * Login of User
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'      => 'required',
            'password'      => 'required',
            'device_type'   => 'required'
        ]);

        // If validation fail, throw message
        if ($validator->fails())
        {
            return $this->respondInternalError("Invalid Input !");
        }

        $credentials    = $request->only('username', 'password');
        $user           = $this->userMeta->where([
                            'username'         => $credentials['username'],
                            'password'      => md5($credentials['password'])
                        ])->first();

        if($user)
        {
            // Check if user is blocked or not
            if($user->usertype == 'staff')
            {
                $staff = $this->staff->where('staff_id', $user->userid)->first();

                // If blocked throw warning
                if($staff->staff_access_status == 0)
                {
                    $this->createLoginAttempt($request, 0, 2);

                    return $this->respondInternalError("Your Access is Blocked, If you have any questions, please contact us.");
                }

                $this->createLoginAttempt($request, 1, 2);
            }
            else if($user->usertype == 'member')
            {
                $member = $this->member->where('userid', $user->userid)->first();

                // If blocked throw warning
                if($member->login_status == 0)
                {
                    $this->createLoginAttempt($request, 0, 3);

                    return $this->respondInternalError("Your Access is Blocked, If you have any questions, please contact us.");
                }

                $this->createLoginAttempt($request, 1, 3);
            }
            else
            {
                $this->createLoginAttempt($request, 1, 1);
            }

            if (!$token = JWTAuth::fromUser($user))
            {
                return $this->respondInternalError("Failed to create token.");
            }

            $responseData = $this->userTransformer->transformToken($token);
            return $this->ApiSuccessResponse($responseData);
        }
        else
        {
            $this->createLoginAttempt($request, 0);

            return $this->respondInternalError("No Such User Found.");
        }

    }

    /**
     * Check User
     */
    public function checkUser()
    {
        $user = JWTAuth::parseToken()->authenticate();
        dd($user);
    }

    /**
     * Create Login attempt
     *
     * @param $request
     * @param $success
     * @param int $userType
     * @return bool
     */
    public function createLoginAttempt($request, $success, $userType = 0)
    {
        $input = $request->all();

        $this->loginAttempt->insert([
            'IP' => $_SERVER["REMOTE_ADDR"],
            'username' => $input['username'],
            'userType' => $userType,
            'Attempts' => 1,
            'loginsuccess' => $success,
            'loginvia' => ($input['device_type'] == 'android') ? 3 : 2,
            'sessionid' => time()  // @to-do - Need to think
        ]);

        return true;
    }
}