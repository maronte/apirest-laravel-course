<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Throwable;

class Handler extends ExceptionHandler
{

    use ApiResponser;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {   

        if ($exception instanceof ValidationException){
            
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        elseif($exception instanceof ModelNotFoundException){
            $modelo = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse(
                "El recurso ${modelo} al que intentas acceder no existe", 
                404);
        }

        elseif ($exception instanceof AuthenticationException) {
            return $this->errorResponse("No estás autenticado", 401);
        }

        elseif ($exception instanceof AuthorizationException) {
            return $this->errorResponse("No tienes permisos para esta acción", 403);
        }

        elseif ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse("No se encontró la URL especificada", 404);
        }
       
        elseif ($exception instanceof HttpException) {
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        elseif ($exception instanceof QueryException && $exception->errorInfo[1] == 1451) {
            return $this->errorResponse('No se puede eliminar de forma permanente el recurso'.
            ' seleccionado porque está relacionado con algún otro', 409);
        }

        if( config('app.debug') ){
            return parent::render($request, $exception);
        }
        return $this->errorResponse("Falla inesperada, por favor intente después", 500);
    }

        /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException;  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        
        $errors = $e->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422);

    }
}
