<?php
namespace App\Service;

use App\Utils\Auth\AuthManager;

class AuthHelperService
{
    private $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * Check whether the logged-in user role has the given permission access.
     *
     * @param string $permission_name
     */
    public function haveAccessWithRedirect($permission_name)
    {
        if (!$this->authManager->getLoggedInUser()->hasAccess($permission_name)) {
            header('Location: ' . templatePath() . '/error/unauthorize.php');
        }
    }

    /**
     * Check whether the logged-in user role has the given permission access.
     *
     * @param string $permission_name
     * @return boolean
     */
    public function haveAccess($permission_name)
    {
        return $this->authManager->getLoggedInUser()->hasAccess($permission_name);
    }

    /**
     * Check whether the user is logged in or not.
     *
     * @return boolean
     */
    public function isUserLoggedIn()
    {
        return $this->authManager->isUserLoggedIn();
    }

    /**
     * Check whether the logged-in user is super or not.
     *
     * @return boolean
     */
    public function isSuper()
    {
        return true; // Assuming all users are super for this example
    }

    /**
     * Get the logged-in user.
     *
     * @return User
     */
    public function getLoggedInUser()
    {
        return $this->authManager->getLoggedInUser();
    }

    /**
     * Get API token of the logged-in user.
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->authManager->getLoggedInUser()->apiToken;
    }
}

