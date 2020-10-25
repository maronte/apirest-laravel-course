<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ApiResponser
{
    private function successResponse($data, int $code)
    {
        return response($data, $code);
    }

    protected function errorResponse($message, int $code)
    {
        return response()->json([['message' => $message, 'code' => $code] , $code]);
    }

    protected function showAll(Collection $collection, int $code = 200){
        return $this->successResponse(['data' => $collection], $code);
    }

    protected function showOne(Model $instance, int $code = 200){
        return $this->successResponse(['data' => $instance], $code);
    }

    protected function showMessage($message, int $code = 200)
    {
        return response()->json([['message' => $message] , $code]);
    }
}