<?php

/**
 * The Default Example Controller Class
 *
 * @author Faizan Ayubi, Hemant Mann
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Music extends Controller {
    /**
     * Initialized Youtube API Client
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        $configuration = Registry::get("configuration");
        $parsed = $configuration->parse("configuration/google");
        
        $client = new Google_Client();
        $client->setDeveloperKey($parsed->google->developer->key);
        $youtube = new Google_Service_YouTube($client);

        Registry::set("gClient", $client);
        Registry::set("youtube", $youtube);
    }

    public function search() {
        $this->seo(array("title" => "Music Search"));
        $view = $this->getActionView();

        $q = RequestMethods::get("q");
        $results = Shared\Services\Youtube::search($q);
        if (is_string($results)) {
            $songs = array();
        } else {
            $songs = $results;
        }
        $view->set("songs", $songs)
            ->set("query", $q);
    }

    public function view($title = '', $id = null) {
    	$this->seo(array("title" => "Music Search"));
        $view = $this->getActionView();

        if (!$id) $this->redirect("/");
        
        $view->set("title", $title)
            ->set("id", $id);
    }

    public function convert($youtubeid='') {
    	$this->seo(array("title" => "Music Search"));
        $view = $this->getActionView();
    }

    public function download($youtubeid='') {
    	$this->seo(array("title" => "Music Search"));
        $view = $this->getActionView();
    }

    public function trending() {
    	$this->seo(array("title" => "Music Search"));
        $view = $this->getActionView();
    }

}
