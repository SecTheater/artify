<?php

namespace Artify\Artify\Tenant\Middleware;

use Artify\Artify\Contracts\Models\Tenant;
use Artify\Artify\Tenant\Events\TenantIdentified;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Str;

class SetTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        optional($this->resolveTenant($request->tenant ?? session('tenant')), function ($tenant) {
            if (!auth()->user()->{Str::plural(app(Tenant::class)->getTable())}->contains('id', $tenant->id)) {
                throw new AuthenticationException(
                    'Unauthenticated.',
                    [],
                    $this->redirectTo($request)
                );
            }
            event(new TenantIdentified($tenant));
        });

        return $next($request);
    }
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('home');
        }
    }
    protected function resolveTenant($uuid)
    {
        $tenant = app(Tenant::class);
        return $tenant->where($tenant->getRouteKeyName(), $uuid)->first();
    }
}
