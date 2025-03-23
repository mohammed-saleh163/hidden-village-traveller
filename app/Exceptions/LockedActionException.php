<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LockedActionException extends Exception
{
    
    public function render(Request $request): Response {
        $status = 423;
        $error = "This action is locked";

        return response(["error" => $error], $status);
    }

}
