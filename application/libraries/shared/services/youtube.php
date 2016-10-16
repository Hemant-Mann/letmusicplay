<?php

namespace Shared\Services;
use Framework\Registry as Registry;
use Framework\ArrayMethods as ArrayMethods;
use YTDownloader\Service\Download as Downloader;

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
                $results[$d['id']] = ArrayMethods::toObject($d);
            }
            return $results;
        } catch (Google_Service_Exception $e) {
        	return "Error";
        } catch (Google_Exception $e) {
        	return "Error";
        }
	}

    /**
     * @return array
     */
    public static function formats($id = '') {
        $url = "https://www.youtube.com/watch?v=";
        try {
            $ytdl = new Downloader($url . $id);
            $video = $ytdl->availableQualities();
            return ['video' => $video, 'audio' => $ytdl->bestMp3];
        } catch (\Exception $e) {
            return ['video' => [], 'audio' => 140];
        }
    }

    public static function download($id = '', $opts = array()) {
        $url = "https://www.youtube.com/watch?v=";
        $ytdl = new Downloader($url . $id);
        switch ($opts['action']) {
            case 'video':
                $file = $ytdl->download($opts['fmt'], $opts['extension']);
                break;

            case 'audio':
                $file = $ytdl->convert($opts['extension'], $opts['fmt']);
                break;
        }
        $file = Downloader::getDownloadPath() . $file;
        return $file;
    }
}
