<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonHttpException extends HttpException
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        $arResponse = [
            'error' => $this->getMessage()
        ];

        return response()->json($arResponse, $this->getStatusCode());
    }
}
