<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\UserModel; // Assuming your UserModel is directly in App\Models


/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 * class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
     protected $helpers = ['url', 'form', 'common']; // Added 'common' helper

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session; // Declare the session property

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
        $this->session = service('session'); // Initialize the session

        // --- Start: Basic Authentication and Authorization Check ---

        // Check if user is logged in
        if (! $this->session->get('isLoggedIn')) {
            // If not logged in and trying to access anything other than a public page (e.g., login, register),
            // redirect them to the login page.
            // Adjust 'login' route as per your application's actual login URL.
            // Ensure public routes (like login, register) do not extend BaseController or bypass this check.
            // FIX: Correctly access the URI path
            if ($request->getUri()->getPath() !== 'login' && $request->getUri()->getPath() !== 'register') { // Adjust paths as needed
                return redirect()->to(site_url('login'))->with('error', 'Please log in to access this page.');
            }
        } else {
            // User is logged in, now check roles for pharmacy access
            // FIX: Correctly access the URI path for segment
            $currentRoute = $request->getUri()->getSegment(1); // Gets the first segment of the URL, e.g., 'pharmacy'

            if ($currentRoute === 'pharmacy') {
                $userRoleId = $this->session->get('role_id'); // Assuming role_id is stored in session

                // If role_id is not in session, fetch it.
                // This is less efficient, better to store role_name directly in session upon login.
                // If you already store role_name, use that instead of fetching.
                $userRoleName = $this->session->get('role_name');

                if (empty($userRoleName) && !empty($userRoleId)) {
                    $userModel = new UserModel(); // Assuming your UserModel is here
                    $roleData = $userModel->db->table('roles')->where('id', $userRoleId)->get()->getRow();
                    if ($roleData) {
                        $userRoleName = $roleData->name;
                        $this->session->set('role_name', $userRoleName); // Store for future use
                    }
                }

                // Define allowed roles for the entire 'pharmacy' group
                $allowedPharmacyRoles = ['Admin', 'Pharmacist', 'Pharmacy_Manager', 'Pharmacy_Sales_Person']; // Ensure these match your roles table 'name' column

                if (!in_array($userRoleName, $allowedPharmacyRoles)) {
                    // Redirect or show an access denied message
                    return redirect()->to(site_url('/'))->with('error', 'You do not have permission to access the pharmacy module.');
                }
            }
        }

        // --- End: Basic Authentication and Authorization Check ---
    }
}
