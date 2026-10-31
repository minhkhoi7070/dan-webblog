<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StartScopedSession extends StartSession
{
    public function __construct(SessionManager $manager, CacheFactory $cache)
    {
        parent::__construct($manager, fn (): CacheFactory => $cache);
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next): Response
    {
        $webManager = $this->manager;
        $sessionConfig = config('session');

        $webManager->driver();

        Auth::shouldUse('web');

        try {
            if ($request->is('admin', 'admin/*')) {
                config([
                    'session.cookie' => $sessionConfig['cookie'].'_admin',
                    'session.path' => '/admin',
                ]);

                if (! app()->bound('session.admin')) {
                    app()->instance('session.admin', new SessionManager(app()));
                }

                $adminManager = app('session.admin');
                $this->manager = $adminManager;
                Auth::shouldUse('admin');
            }

            app()->instance('session', $this->manager);
            app()->instance('session.store', $this->manager->driver());
            app('redirect')->setSession($this->manager->driver());
            app('cookie')->setDefaultPathAndDomain(
                config('session.path'), config('session.domain'), config('session.secure'), config('session.same_site'),
            );

            $response = parent::handle($request, $next);

            if ($request->is('admin', 'admin/*')) {
                $response->headers->set('Cache-Control', 'no-store, private');
            }

            return $response;
        } finally {
            $this->manager = $webManager;
            app()->instance('session', $webManager);
            config(['session' => $sessionConfig]);
            app('cookie')->setDefaultPathAndDomain(
                $sessionConfig['path'], $sessionConfig['domain'], $sessionConfig['secure'], $sessionConfig['same_site'],
            );
            Auth::shouldUse('web');
        }
    }
}
