<?php

namespace Shared\Services;
use Framework\Registry as Registry;
use Framework\ArrayMethods as ArrayMethods;

class Youtube {
	/**
	 * @param string $q search query
	 * @param array $opts Extra options to be passed to API
	 * @return array
	 */
	public static function search($q, $opts = array()) {
		$youtube = Registry::get("youtube");

        try {
            $searchResponse = $youtube->search->listSearch('id,snippet', array(
                'q' => $q,
                'maxResults' => isset($opts['maxResults']) ? $opts['maxResults'] : 50,
                'type' => 'video'
            ));

            $results = array();
            foreach ($searchResponse['items'] as $searchResult) {
                $thumbnail = $searchResult['snippet']['thumbnails']['medium']['url'];
                $title = $searchResult['snippet']['title'];
                $href = $searchResult['id']['videoId'];

                $d = array(
                    "img" => $thumbnail,
                    "title" => $title,
                    "id" => $href
                );
                $results[] = ArrayMethods::toObject($d);
            }
            return $results;
        } catch (Google_Service_Exception $e) {
        	return "Error";
        } catch (Google_Exception $e) {
        	return "Error";
        }
	}
}
