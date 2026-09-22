<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;

trait ApiResponseTrait
{
    /**
     * Return a standardized success JSON response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @param array $extra
     * @return JsonResponse
     */
    public function successResponse($data = [], ?string $message = 'Operation successful.', int $code = 200, array $extra = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        if (!empty($extra)) {
            $response = array_merge($response, $extra);
        }

        return response()->json($response, $code);
    }

    /**
     * Return a standardized error JSON response.
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @param array $extra
     * @return JsonResponse
     */
    public function errorResponse(string $message = 'An error occurred.', int $code = 400, $errors = null, array $extra = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        if (!empty($extra)) {
            $response = array_merge($response, $extra);
        }

        return response()->json($response, $code);
    }

    /**
     * Return a validation error JSON response.
     *
     * @param Validator|array $validator
     * @param string $message
     * @return JsonResponse
     */
    public function validationErrorResponse($validator, string $message = 'Validation failed.'): JsonResponse
    {
        $errors = is_array($validator) ? $validator : $validator->errors()->toArray();

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }
}
