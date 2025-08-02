<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectToAppropriatePanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply this middleware if user is authenticated
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $currentPath = $request->path();
        
        // Get the user's role
        $role = $user->role->name ?? null;
        
        // Define panel paths for each role
        $panelPaths = [
            'admin' => 'admin',
            'staff' => 'staff', 
            'borrower' => 'user'
        ];
        
        // If user has a role and we're not already on their panel
        if ($role && isset($panelPaths[$role])) {
            $userPanelPath = $panelPaths[$role];
            
            // Check if we're on a different panel path
            $isOnDifferentPanel = false;
            foreach ($panelPaths as $roleName => $panelPath) {
                if ($roleName !== $role && str_starts_with($currentPath, $panelPath)) {
                    $isOnDifferentPanel = true;
                    break;
                }
            }
            
            // If we're on a different panel or trying to access a non-existent route
            if ($isOnDifferentPanel || $this->isNonExistentRoute($request)) {
                return redirect()->to("/{$userPanelPath}");
            }
        }
        
        return $next($request);
    }
    
    /**
     * Check if the current route doesn't exist
     */
    private function isNonExistentRoute(Request $request): bool
    {
        try {
            $route = app('router')->getRoutes()->match($request);
            return false; // Route exists
        } catch (\Exception $e) {
            return true; // Route doesn't exist
        }
    }
} 