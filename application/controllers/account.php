<?php

/**
 * The User Account Class
 *
 * @author Faizan Ayubi, Hemant Mann
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;
use Shared\Services\Youtube as Youtube;

class Account extends Controller {

	public function index() {
		$this->seo(array("title" => "Profile"));
		$view = $this->getActionView();
	}
	
	public function create() {
		$this->seo(array("title" => "Create Account"));
		$view = $this->getActionView();

		if (RequestMethods::post("action") == "create") {
			$users = Registry::get("MongoDB")->users;
			$record = $users->findOne(array('email' => RequestMethods::post("email")));
			if (!isset($record)) {
			    $users->insert([
			        'name' => RequestMethods::post("name"),
			        'email' => RequestMethods::post("email"),
			        'password' => RequestMethods::post("name"),
			        'created' => new \MongoDate(),
			        'live' => 1
			    ]);
			    $view->set("message", "User Registered Successfully");
			}
		}
	}

	public function apikey() {
		$this->seo(array("title" => "Generate API Key"));
		$view = $this->getActionView();
	}
}
