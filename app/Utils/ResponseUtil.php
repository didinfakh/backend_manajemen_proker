<?php

namespace App\Utils;

use Illuminate\Http\JsonResponse;

trait ResponseUtil
{
    /**
     * Return generic json response.
     *
     * @param $data
     * @param int $code
     * @param string|null $message
     * @return JsonResponse
     */
    public function respond($data, $code = 200, $message = null, $extra = []): JsonResponse
    {
        $response = [
            'success' => $code >= 200 && $code < 300,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($extra)) {
            $response = array_merge($response, $extra);
        }

        return response()->json($response, $code);
    }

    /**
     * Send a success response.
     *
     * @param $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    public function success($data, $message = null, $code = 200): JsonResponse
    {
        return $this->respond($data, $code, $message);
    }

    /**
     * Send a failure/error response.
     *
     * @param $error
     * @param int $code
     * @return JsonResponse
     */
    public function fail($error, $code = 404): JsonResponse
    {
        return $this->respond(null, $code, $error);
    }

    /**
     * Send a not found response.
     *
     * @param string $message
     * @return JsonResponse
     */
    public function failNotFound($message = 'Not Found'): JsonResponse
    {
        return $this->fail($message, 404);
    }

    /**
     * Send a created response.
     *
     * @param $data
     * @param string $message
     * @return JsonResponse
     */
    public function respondCreated($data, $message = 'Created'): JsonResponse
    {
        return $this->respond($data, 201, $message);
    }

    /**
     * Send a deleted response.
     *
     * @param $data
     * @param string $message
     * @return JsonResponse
     */
    public function respondDeleted($data, $message = 'Deleted'): JsonResponse
    {
        return $this->respond($data, 200, $message);
    }
}
