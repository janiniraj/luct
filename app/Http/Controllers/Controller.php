<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Mockery\Exception;
use Response;
use Ixudra\Curl\Facades\Curl;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * default status code
     *
     * @var integer
     */
    protected $statusCode = 200;
    /**
     * default status code
     *
     * @var integer
     */
    protected $successMessage = 'Success';
    /**
     * get the status code
     *
     * @return statuscode
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    /**
     * get the status code
     *
     * @return statuscode
     */
    public function getSuccessMessage()
    {
        return $this->successMessage;
    }
    /**
     * set the Success Message
     *
     * @param String $successMessage
     * @return mix
     */
    public function setSuccessMessage($successMessage)
    {
        $this->successMessage = $successMessage;
        return $this;
    }
    /**
     * set the status code
     *
     * @param [type] $statusCode [description]
     * @return mix
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }
    /**
     * responsd not found
     *
     * @param  string $message
     * @return mix
     */
    public function respondNotFound($message = "Not Found")
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->respondWithError($message);
    }
    /**
     * Respond with error
     *
     * @param  string $message
     * @return mix
     */
    public function respondInternalError($message = "Internal Error")
    {
        return $this->setStatusCode('500')->respondWithError($message);
    }
    /**
     * Respond
     *
     * @param  array $data
     * @param  array  $headers
     * @return mix
     */
    public function respond($data, $headers = [])
    {
        //return Response::json($data, $this->getStatusCode(), $headers);
        return Response::json($data,200,$headers,JSON_NUMERIC_CHECK);
    }

    /**
     * Respond
     *
     * @param  array $data
     * @param  array  $headers
     * @return mix
     */
    public function ApiSuccessResponse($data = array(), $headers = [])
    {
        $response['message']            = $this->getSuccessMessage();
        $response['code']               = $this->getStatusCode();
        $response['response']['data']   = $data;
        //return Response::json($response, $this->getStatusCode(), $headers);
        return Response::json($response,200,$headers,JSON_NUMERIC_CHECK);
    }
    /**
     * respond with pagincation
     *
     * @param Data $data
     * @param Paginator $paginator
     * @return mix
     */
    public function respondWithPagination($data, $paginator, $headers = [])
    {
        $response['message']    = $this->getSuccessMessage();
        $response['response']   = [
            'data'          => $data,
            'paginator'     => [
                        'current_page'  => $paginator['current_page'],
                        'total_pages'   => $paginator['total_pages']
                    ]
        ];
        $response['code']       = $this->getStatusCode();

        return Response::json($response,200,$headers,JSON_NUMERIC_CHECK);
    }
    /**
     * respond with error
     *
     * @param $message
     * @return mix
     */
    public function respondWithError($message)
    {
        return $this->respond([
            'message' => $message,
            'code' => $this->getStatusCode()
        ]);
    }
    /**
     * Respond Created
     *
     * @param  string $message
     * @return mix
     */
    public function respondCreated($message)
    {
        return $this->setStatusCode(201)->respond([
            'message' => $message
        ]);
    }
    /**
     * Throw Validation
     *
     * @param  string $message
     * @return mix
     */
    public function throwValidation($message)
    {
        return $this->setStatusCode(422)
            ->respondWithError($message);
    }

    /**
     * get Response as Array from URL
     *
     * @param $url
     * @return bool|mixed
     */
    public function getResponseFromUrl($url)
    {
        $response   = Curl::to($url)->get();
        if($this->isValidXml($response))
        {
            $xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
            return json_decode(json_encode((array)$xml), TRUE);
        }
        else
        {
            return false;
        }
    }

    /**
     * Check Valid XML or not
     *
     * @param $content
     * @return bool
     */
    public function isValidXml($content)
    {
        $content = trim($content);
        if (empty($content))
        {
            return false;
        }

        if (stripos($content, '<!DOCTYPE html>') !== false)
        {
            return false;
        }

        libxml_use_internal_errors(true);
        simplexml_load_string($content);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        return empty($errors);
    }
}
