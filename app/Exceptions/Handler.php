<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Httpkernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\AuthenticationException;
use Throwable;


class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    public function render($request, Throwable $exception)
    {
        
        if ($request->expectsJson()){

            //1.model找不到資源
            if ($exception instanceof ModelNotFoundException){
                return $this->errorResponse(
                    '找不到資源',
                    Response::HTTP_NOT_FOUND
                );
            }   
        

            //2.網址輸入錯誤
            if ($exception instanceof NotFoundHttpException){
                return $this->errorResponse(
                    '網址輸入錯誤',
                    Response::HTTP_NOT_FOUND
                );
            }   

            //3.網站不允許該請求
            if ($exception instanceof MethodNotAllowedHttpException){
                return $this->errorResponse(
                    $exception->getMessage(),
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            }  
        
            if ($exception instanceof AuthenticationException) {
                return response()->json(['message' => 'please check your token']);
            }
            
            return parent::render($request, $exception);
        }
        
        if ($exception instanceof AuthenticationException) {

            if ($request->is('api/*')) {
                //return response()->json(['error' => 'Not Found'], 404);
                return response()->json(['message' => 'please check your token,and use Bearer Token'],401);
            }
        }
        
        //執行父類別render程式
        return parent::render($request, $exception);     
    }

    /*與56行同義
    protected function unauthenticated($request, AuthenticationException $ex){

        if( $request->is('api/*') ) { // for routes starting with `/api`
            //return response()->json(['success' => false, 'message' => $ex->getMessage()], 401);
            return response()->json(['message' => 'faild to logged out,please check your token'],401);
        }
        //return redirect('/login'); // for normal routes 
    }
    */


    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {

        $this->renderable(function (\Exception $e) {
            if ($e->getPrevious() instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect()->route('login');
            };
        });

        //$this->reportable(function (Throwable $e) {
            //
        //});
    }
}

