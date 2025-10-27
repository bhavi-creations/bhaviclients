<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    public string $baseURL = 'bhaviclients.bhavicreations.com';

    public array $allowedHostnames = [];

    public string $indexPage = 'index.php';

    public string $uriProtocol = 'REQUEST_URI';

    public string $permittedURIChars = 'a-z 0-9~%.:_\-';

    public string $defaultLocale = 'en';

    public bool $negotiateLocale = false;

    public array $supportedLocales = ['en'];

    public string $appTimezone = 'Asia/Kolkata';

    public string $charset = 'UTF-8';

    public bool $forceGlobalSecureRequests = false;

    public array $proxyIPs = [];

    public bool $CSPEnabled = false;

    // ============================================================
    // SESSION CONFIGURATION (ADDED)
    // ============================================================


    public string $sessionDriver = 'CodeIgniter\Session\Handlers\DatabaseHandler';
    public string $sessionSavePath = 'ci_sessions';
 

    public string $sessionCookieName = 'ci_session';


    public int $sessionExpiration = 7200;
 

    public bool $sessionMatchIP = false;

    public int $sessionTimeToUpdate = 300;

    public bool $sessionRegenerateDestroy = false;

    // ============================================================
    // COOKIE CONFIGURATION
    // ============================================================

    public string $cookiePrefix = '';

    public string $cookieDomain = '';

    public string $cookiePath = '/';

    public bool $cookieSecure = false;

    public bool $cookieHTTPOnly = true;

    public string $cookieSameSite = 'Lax';
}
