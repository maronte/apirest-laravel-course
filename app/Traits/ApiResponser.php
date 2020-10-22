<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponser
{
    private function successResponse($data, int $code)
    {
        return response($data, $code);
    }

    protected function errorResponse(string $message, int $code)
    {
        return response()->json([['message' => $message, 'code' => $code] , $code]);
    }

    protected function showAll(Collection $collection, int $code = 200){
        return $this->successResponse(['data' => $collection], $code);
    }

    protected function showOne(Model $instance, int $code = 200){
        return $this->successResponse(['data' => $instance], $code);
    }
}