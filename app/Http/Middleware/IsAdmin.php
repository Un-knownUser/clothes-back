<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, авторизован ли пользователь и является ли он админом
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Если это не админ, возвращаем 403 Forbidden (Доступ запрещен)
        return response()->json([
            'message' => 'Доступ запрещен. Требуются права администратора.'
        ], 403);
    }
}
