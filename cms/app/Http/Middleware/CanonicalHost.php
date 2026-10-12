<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Laravel builds every link and redirect from the Host header of the request. When the site is reached
 * under another name (for example "localhost" through a tunnel or proxy), the admin login would send the
 * browser to that other name. In production this sends such requests to the address set in APP_URL instead.
 */
class CanonicalHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $canonical = config('app.url');
        $parts = $canonical ? parse_url($canonical) : false;

        if (! $parts || empty($parts['host']) || app()->environment(['local', 'testing'])) {
            return $next($request);
        }

        $port = $parts['port'] ?? (($parts['scheme'] ?? 'http') === 'https' ? 443 : 80);
        $sameHost = strcasecmp($request->getHost(), $parts['host']) === 0;
        $samePort = (int) $request->getPort() === (int) $port;

        if ($sameHost && $samePort) {
            return $next($request);
        }

        // 308 keeps the method and body, so a form post is not turned into a GET.
        return redirect()->to(rtrim($canonical, '/').$request->getRequestUri(), 308);
    }
}
