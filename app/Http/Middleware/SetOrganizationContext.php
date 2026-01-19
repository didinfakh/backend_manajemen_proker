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

        if (!$orgId) {
            abort(400, 'X-Organization-Id header is required');
        }

        // Optional: validasi organization ada
        // $org = Organization::find($orgId);
        // if (!$org) {
        //     abort(403, 'Invalid organization');
        // }

        // Simpan di container
        app()->instance('id_organization', (int) $orgId);

        return $next($request);
    }
}
