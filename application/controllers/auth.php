<?php

/**
 * Auth Controller
 *
 * @author Hemant Mann
 */
use Framework\RequestMethods as RequestMethods;
use Framework\Registry as Registry;
use Framework\ArrayMethods as ArrayMethods;
use Framework\StringMethods as StringMethods;

class Auth extends \Shared\Controller {
	/**
	 * @protected
	 */
	public function _admin() {
		$this->_secure();
		if (!$this->user->admin) {
			$this->redirect("/");
		}
		$this->setLayout("layouts/admin");
	}

	/**
	 * @protected
	 */
	public function _session() {
		if ($this->user) {
			if (isset($this->user->admin)) {
				$redirect = "/admin";
			} else {
				$redirect = "/account";
			}

			$this->redirect($redirect);
		}
	}

	/**
	 * @before _session
	 */
	public function login() {
		$this->willRenderLayoutView = false;
		$view = $this->getActionView();


		if (RequestMethods::post("action") == "login") {
			$email = RequestMethods::post("email");
			$password = RequestMethods::post("password");

			$users = Registry::get("MongoDB")->users;
			$record = $users->findOne(['email' => $email]);

			if ($record) {
				$record = ArrayMethods::toObject($record);
				if (StringMethods::checkHash($password, $record->password)) {
					$this->setUser($record);

					$redirect = isset($record->admin) ? "/admin" : "/account";

					$this->redirect($redirect);
				} else {
					$view->set("error", "Invalid email/password");
				}
			} else {
				$view->set("error", "Invalid email/password");
			}
		}
	}

	public function logout() {
		$this->setUser(false);
		session_destroy();
		$this->redirect("/");
	}

	/**
	 * @before _admin
	 */
	public function manageUsers() {
		$this->seo(["title" => "Manage Users"]);
		$view = $this->getActionView();
		$page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);

        $property = RequestMethods::get("property", "live");
        $val = RequestMethods::get("value", 1);

		$where = ["{$property}" => $val];
        $users = Models\User::all($where, [], "created", -1, $limit, $page);
        $count = Models\User::count($where);
        $view->set([
            "users" => $users,
            "page" => $page,
            "limit" => $limit,
            "count" => $count,
            "property" => $property,
            "val" => $val
        ]);
	}

	/**
     * @before _secure
     */
    public function authenticate() {
        $this->willRenderLayoutView = false;
        $view = $this->getActionView(); $session = Registry::get("session");
        $redirect = $session->get('Authenticate:$redirect');
        if (!$redirect) {
            $this->redirect("/404");
        }

        $tries_key = 'Auth\SudoMode:$tries';
        $tries = $session->get($tries_key, 1);

        $proceed = false;
        if (!isset($_COOKIE['sudo_mode']) && RequestMethods::post("action") == "verify") {
            $password = RequestMethods::post("password");
            if ($tries >= 3) {
                $session->erase($tries_key);
                $this->redirect("/auth/logout");
            }

            if (StringMethods::checkHash($password, $this->user->password)) {
                setcookie('sudo_mode', 'enabled', time() + 60 * 30);
                $session->set('Authenticate:$done', true);
                $proceed = true;
            } else {
                $session->set($tries_key, ++$tries);
                $view->set("error", "Authentication failed");
            }
        } elseif ($_COOKIE['sudo_mode'] == 'enabled') {
            $proceed = true;
        }

        if ($proceed) {
            $this->redirect($redirect);
        }
    }
}