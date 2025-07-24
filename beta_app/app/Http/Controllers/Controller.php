<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Common response format for JSON APIs
     */
    protected function jsonResponse($data = null, $message = null, $status = 200, $meta = [])
    {
        $response = [
            'success' => $status >= 200 && $status < 300,
            'status' => $status,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $status);
    }

    /**
     * Success response
     */
    protected function success($data = null, $message = null, $meta = [])
    {
        return $this->jsonResponse($data, $message, 200, $meta);
    }

    /**
     * Error response
     */
    protected function error($message, $status = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'status' => $status,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Handle pagination meta data
     */
    protected function paginationMeta($paginator)
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_next_page' => $paginator->hasMorePages(),
            'has_prev_page' => $paginator->currentPage() > 1,
        ];
    }

    /**
     * Determine if request expects JSON response
     */
    protected function wantsJson(Request $request)
    {
        return $request->expectsJson() || $request->is('api/*');
    }
}
