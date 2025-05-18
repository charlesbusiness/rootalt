<?php

namespace Modules\Authentication\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Models\Resource;
use Modules\Core\Services\CoreService;

class ResourceAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Retrieve the route name from the current route.
        $routeName = $request->route()->getName();

        // If no route name is set, you may either bypass or deny—here we choose to bypass.
        if (!$routeName) {
            return $next($request);
        }

        $user = $request->user('user');

        if (!$user) {
            return failedResponse(null, 'Unauthorised access', Response::HTTP_FORBIDDEN);
        }

        if ($user->hasSuperAccessToResource()) {
            return $next($request);
        }

        $userRole = $user->businessRoles;
        if (!count($userRole)) {
            return failedResponse(null, 'Access denied for user', 403);
        }

        $action  =  explode('@',  $request->route()->getAction()['uses'])[1];
        $matchingEndpoint = Resource::query()->whereEndpoint($action)->first();

        $resourceId = $matchingEndpoint->id;
        $businessId = $userRole[0]['business_id'];

        // Check if user has access based on these parameters
        if (!$user || !$user->hasAccessToResource($resourceId, $businessId)) {
            return failedResponse(null, 'Access denied for user', 403);
        }

        return $next($request);
    }
}
