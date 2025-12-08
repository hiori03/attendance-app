<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\ListController as AdminListController;
use App\Http\Controllers\Staff\ListController as StaffListController;

class RequestListMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $request->merge(['controller' => AdminListController::class]);
        } else {
            $request->merge(['controller' => StaffListController::class]);
        }

        return $next($request);
    }
}
