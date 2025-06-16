<?php

namespace App\Utils\Auth;


use App\Utils\CoreDataService;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Utils\Auth\User;
use App\Utils\DBManager;
use App\Utils\Logger;
use stdClass;

class AuthManager extends CoreDataService
{
	/**
	 * holds logged in user object
	 * @var User
	 */
	private $user;

	private $session;

	private string $sessionKey;



	public function __construct(RequestStack $requestStack, string $sessionKey, private DBManager $dbmanager)
	{
		parent::__construct($dbmanager);
	
		$this->initSession($requestStack);
		$this->sessionKey = $sessionKey;
	}

	private function initSession(RequestStack $requestStack)
	{
		$request = $requestStack->getCurrentRequest();
		if ($request?->hasSession()) {
			$this->session = $request->getSession();
		} else {
			$this->session = new Session();

			// Attach the new session to the request if available
			$request?->setSession($this->session);
		}

		$this->session->start();
	}

	/**
	 * Perform authentication based on credential supplied
	 * if user found set User object in session or as of now simply set user_id in session
	 * @param  string $user_id
	 * @param  string $password
	 * @return boolean
	 */
	public function performAuth($email, $password)
	{


		$auth_sql = 'SELECT id, first_name, last_name,email,password, member_role_id,api_token FROM members 	WHERE  email=:email';
		$user = $this->executeSQL($auth_sql, ['email' => $email], true);

		if ($user !== null && $user !== false && !empty($user)) {
			if (password_verify($password, $user['password'])) {
				//User is successfully logged in all auth params are right
				//store user object inside session

				// prepare user object
				$this->user = new stdClass();
				$this->user->id  =  $user['id'];
				$this->user->firstName = $user['first_name'];
				$this->user->lastName = $user['last_name'];
				$this->user->userId = $user['email'];
				$this->user->password = $user['password'];
				$this->user->userRoleId = $user['member_role_id'];
				$this->user->apiToken = $user['api_token'];
				//store user object
				$this->session->set($this->sessionKey, $this->user);

				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * fetch logged in user object from session object
	 * @return User
	 */
	public function getLoggedInUser(): User
	{

		$userData = (array) $this->session->get($this->sessionKey);
		return new User($userData, $this->dbmanager);
	}
	/**
	 *  Get logged in user role
	 * @return Role
	 */
	public function getLoggedInUserRole()
	{
		$logged_in_user = $this->getLoggedInUser();
		return $logged_in_user->getRole();
	}
	/**
	 * check session manager for logged in user bject
	 * @return boolean
	 */
	public function isUserLoggedIn(): bool
	{
		return $this->session->has($this->sessionKey);
	}

	/**
	 * Logout user
	 * Remove logged in user object from session
	 * @return
	 */
	public function logout()
	{
		$this->session->clear();
	}
	public function changePassword($new_password)
	{
		$logged_in_user = $this->getLoggedInUser();

		$new_password = password_hash($new_password, PASSWORD_BCRYPT);
		$change_password_sql = 'Update members SET password=:new_password WHERE id=:User_Id';


		return $this->performDBUpdate($change_password_sql, ['new_password' => $new_password, 'User_Id' => $logged_in_user->id]);
	}

	/**
	 *  Get user by Api token
	 */
	public function getUserByApiToken($api_token)
	{
		$user_by_api_token_sql = "SELECT  * FROM members WHERE api_token=:api_token";
		$user = $this->executeSQL($user_by_api_token_sql, ['api_token' => $api_token], true);

		return $user;
	}

	/**
	 *  get API token by email id
	 */
	public function getApiTokenByEmail($email)
	{
		$user_by_email_sql = "SELECT  id,first_name,last_name,api_token,email as user_id,role_id,role,is_super FROM members_view WHERE email=:email";
		$user = $this->executeSQL($user_by_email_sql, ['email' => $email], true);

		return $user;
	}
}
