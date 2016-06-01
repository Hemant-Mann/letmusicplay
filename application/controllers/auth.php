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
		if (!isset($this->user->admin)) {
			$this->redirect("/");
		}
	}

	/**
	 * @protected
	 */
	public function session() {
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
	 * @before session
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
}