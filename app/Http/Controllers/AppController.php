<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AppController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = $this->checkAuthorization($request);
            return $next($request);
        });
    }
}
