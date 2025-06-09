<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserActivity;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check()) {
            UserActivity::create([
                'user_id' => auth()->id(),
                'action' => $this->determineAction($request),
                'model_type' => $this->getModelType($request),
                'model_id' => $this->getModelId($request),
                'data' => json_encode($request->except(['password', 'password_confirmation'])),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);
        }

        return $response;
    }

    private function determineAction(Request $request): string
    {
        return match ($request->method()) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            'GET' => 'view',
            default => 'unknown',
        };
    }

    private function getModelType(Request $request): ?string
    {
        $route = $request->route();
        if ($route && isset($route->parameterNames()[0])) {
            $model = str_replace('_', '', ucwords($route->parameterNames()[0], '_'));
            return "App\\Models\\{$model}";
        }
        return null;
    }

    private function getModelId(Request $request): ?int
    {
        // $route = $request->route();
        // if ($route && isset($route->parameterNames()[0])) {
        //     return intval($route->parameter($route->parameterNames()[0])) ?? null;
        // }
        return null;
    }
}