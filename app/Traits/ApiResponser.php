<?php

namespace App\Traits;


/*
|--------------------------------------------------------------------------
| Api Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for any response we sent to clients.
|
*/

trait ApiResponser
{
    /**
     * Return a success JSON response.
     *
     * @param  string  $message
     * @param  array|string  $data
     * @param  int|null  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse($data = null, string $message = null, int $code = 200)
    {
        return response()->json([
            'status' => 'Success',
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param  string  $message
     * @param  array|string|null  $data
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendErrors($data = null, string $message = null, int $code = 400)
    {
        return response()->json([
            'status' => 'Error',
            'errors' => $data,
            'message' => $message,
        ], $code);
    }

}