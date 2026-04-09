<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Organization;

class SetOrganizationContext
{
    public function handle(Request $request, Closure $next)
    {
        $orgId = $request->header('X-Organization-Id');

        // If not in header, try to get from cache or authenticated user
        if (!$orgId && $request->user()) {
            $user = $request->user();
            $orgId = \Illuminate\Support\Facades\Cache::get("user_org:{$user->id_user}");

            if (!$orgId) {
                $orgId = $user->id_organization;
            }
        }

        if (!$orgId) {
            // For now, only abort if the route is not an open route (optional, based on requirement)
            // But usually this middleware is applied to groups that NEED organization context
            abort(400, 'Organization context (X-Organization-Id header or login session) is required');
        }

        // Simpan di container
        app()->instance('id_organization', (int) $orgId);

        return $next($request);
    }
}
