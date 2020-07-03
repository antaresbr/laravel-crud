<?php

namespace Antares\Crud\Http;

use Antares\Http\JsonResponse;

class CrudJsonResponse
{
    /**
     * Create a error json response
     *
     * @param mixed $code
     * @param mixed $msg
     * @param mixed $data
     * @param mixed $httpStatusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($code, $msg = null, $data = null, $httpStatusCode = null)
    {
        $r = JsonResponse::error(
            CrudHttpErrors::error($code),
            $msg,
            $data,
        );
        if (!empty($httpStatusCode)) {
            $r->setStatusCode($httpStatusCode);
        }
        return $r;
    }

    /**
     * Create a successful json response
     *
     * @param mixed $data
     * @param mixed $msg
     * @param mixed $httpStatusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function successful($data = null, $msg = null, $httpStatusCode = null)
    {
        $r = JsonResponse::successful(
            $data,
            $msg,
        );
        if (!empty($httpStatusCode)) {
            $r->setStatusCode($httpStatusCode);
        }
        return $r;
    }
}
