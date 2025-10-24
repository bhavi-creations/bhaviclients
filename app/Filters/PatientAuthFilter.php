<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PatientAuthFilter implements FilterInterface
{
   

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no action after request
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $currentUri = service('uri')->getPath();
        
        // --- TEMPORARY DEBUG: LOG THE URI PATH ON EVERY HIT ---
        // Look for the URI path that keeps repeating in your log file.
        // Once you find it, you can specifically exclude it.
        log_message('debug', "PatientAuthFilter: Attempting to process URI: " . $currentUri);
        // ------------------------------------------------------


        // ----------------------------------------------------------------------
        // ** CRITICAL FIX: STOP SESSION INITIALIZATION FOR ASSETS **
        // Check 1: Contains the public folder marker (case-insensitive)
        $isPublicAsset = strpos(strtolower($currentUri), 'public/') !== false;
        
        // Check 2: Ends with a common asset extension (e.g., /dashboard/logo.png)
        $assetExtensions = '/(\.ico|\.png|\.jpg|\.jpeg|\.gif|\.css|\.js|\.woff|\.ttf|\.svg|\.map|\.txt|\.json|\.eot|\.otf|\.woff2)$/i';
        $isAssetFile = preg_match($assetExtensions, $currentUri);
        
        // Check 3: Explicitly check for common root assets
        $isRootAsset = in_array($currentUri, ['favicon.ico', 'robots.txt']);

        if ($isPublicAsset || $isAssetFile || $isRootAsset) {
            // Allow the asset request to proceed without loading the session or checking auth.
            return; 
        }
        
        // ----------------------------------------------------------------------
        // If it's not an asset, proceed with security checks
        // ----------------------------------------------------------------------
        $session = session(); // Session initialized here (and now only once per actual page request)
        $isLoggedIn = $session->get('isLoggedIn');
        $roleId = $session->get('role_id');

        // Log only for requests that are controller methods
        log_message('debug', "PatientAuthFilter: Authentication check successful. isLoggedIn={$isLoggedIn}, roleId={$roleId}.");


        // ----------------------------------------------------------------------
        // ** Check Authentication **
        // ----------------------------------------------------------------------
        if (!$isLoggedIn || $roleId != 10) {
            // Redirect them to the public login page
            return redirect()->to('/patient-portal/login')->with('error', 'Please log in to access the patient portal.');
        }
        
        // If checks pass, implicitly return null to continue to the controller method
    }
}
