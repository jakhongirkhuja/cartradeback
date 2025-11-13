<?php

namespace App\Http\Middleware;

use App\Helper\ErrorHelperResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ownerRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = Auth::user()->role;
        if ($role != 'rent' || $role != 'admin') {
            $lang['ru'] = 'Отказано в доступе';
            $lang['uz'] = 'Ruxsat berilmadi';
            return ErrorHelperResponse::returnError($lang, Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
