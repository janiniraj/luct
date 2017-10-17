<?php namespace App\Traits;

/**
 * Trait ResultTrait
 * @package App\Traits
 */
trait ResultTrait
{
    /**
     * Response Code
     * @var int
     */
    public $responseCode = 200;

    /**
     * Response Message
     * @var string
     */
    public $responseMessage = "Success";

    /**
     * Format Result
     *
     * @param string $result
     * @param string $object
     * @return \Illuminate\Http\JsonResponse
     */
	public function result( $result="", $object = '')
	{
		$data = array(
		    "code"      => $this->responseCode,
            "message"   => $this->responseMessage,
            "data"      => $result
        );

		
		if( is_object($object) && $object instanceof \Illuminate\Pagination\LengthAwarePaginator )
		{				
			$data['paginator'] = array(
					'total'         =>$object->total(),
					'perPage'       =>$object->perPage(),
					'currentPage'   =>$object->currentPage(),
					'lastPage'      =>$object->lastPage(),
					'path'          =>$object->resolveCurrentPath()
			);
		}
		
		return response()->json($data);
	}
    
}
