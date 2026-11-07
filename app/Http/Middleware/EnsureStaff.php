<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('staff.login');
        }

        abort_unless(
            $user->is_active && in_array($user->role, [UserRole::Admin, UserRole::Petugas], true),
            Response::HTTP_FORBIDDEN,
        );

        return $next($request);
    }
}
