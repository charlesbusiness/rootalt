<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckResourcePermission
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return failedResponse(null, 'Action not authorised', Response::HTTP_UNAUTHORIZED);
        }

        // Retrieve the route name from the current route.
        $routeName = $request->route()->getName();

        // If no route name is set, you may either bypass or deny—here we choose to bypass.
        if (!$routeName) {
            return $next($request);
        }

        // Assuming the route name is formatted as resource.action (e.g., "businesses.view")
        $parts = explode('.', $routeName);
        if (count($parts) < 2) {
            // If the format doesn't match, bypass permission check.
            return $next($request);
        }

        // Extract resource and action from the route name.
        $resource = strtolower($parts[0]);
        $action = strtolower($parts[1]);

        // Retrieve the user's permissions.
        // Assumes the User model has an accessor `all_permissions` that returns a collection of Permission models.
        $permissions = $user->all_permissions;

        // Check if any permission attached to the user matches the resource and action.
        $hasPermission = $permissions->contains(function ($permission) use ($resource, $action) {
            return $permission->resource
                && strtolower($permission->resource->name) === $resource
                && strtolower($permission->action) === $action;
        });

        if (!$hasPermission) {
            return failedResponse(null, 'Action not authorised', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
