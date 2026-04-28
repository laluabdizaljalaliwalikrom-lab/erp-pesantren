<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGuardianApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('guardian')->user();

        // If not logged in as guardian, continue (other middleware will handle auth)
        if (!$user) {
            return $next($request);
        }

        // If not approved, redirect to the pending page
        // But allow access to the pending page itself and logout
        if (!$user->is_approved) {
            $currentRoute = $request->route()->getName();
            
            // Allow access to the pending page and logout
            if ($currentRoute !== 'filament.guardian.pages.account-pending-approval' && 
                $currentRoute !== 'filament.guardian.auth.logout') {
                return redirect()->route('filament.guardian.pages.account-pending-approval');
            }
        } else {
            // User is approved. If they are on the pending page, redirect them to the dashboard.
            $currentRoute = $request->route()->getName();
            if ($currentRoute === 'filament.guardian.pages.account-pending-approval') {
                return redirect()->to(\Filament\Facades\Filament::getHomeUrl() ?? '/wali');
            }
        }

        return $next($request);
    }
}
