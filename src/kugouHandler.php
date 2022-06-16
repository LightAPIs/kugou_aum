<?php

class AumKugouHandler {
    public static $siteSearch = 'http://lyrics.kugou.com/search?ver=1&man=yes&client=pc&duration=277000&hash=&keyword=';
    public static $siteDownload = 'http://lyrics.kugou.com/download?ver=1&client=pc&fmt=lrc&charset=utf8&';
    public static $siteHeader = array('Host: lyrics.kugou.com');
    public static $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.5005.63 Safari/537.36';

    public static function getContent($url, $defaultValue) {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, AumKugouHandler::$userAgent);
        curl_setopt($curl, CURLOPT_HTTPHEADER, AumKugouHandler::$siteHeader);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);
        curl_close($curl);

        if ($result === false) {
            return $defaultValue;
        } else {
            return $result;
        }
    }

    public static function search($title, $artist) {
        $results = array();
        $url = AumKugouHandler::$siteSearch . urlencode($artist . '-' . $title);
        $jsonContent = AumKugouHandler::getContent($url, '{"candidates":[]}');
        $json = json_decode($jsonContent, true);

        $songArray = $json['candidates'];
        foreach($songArray as $songItem) {
            $song = $songItem['song'];
            $id = 'id=' . $songItem['id'] . '&accesskey=' . $songItem['accesskey'];
            $singers = array();
            foreach (explode('、', $songItem['singer']) as $singer) {
                array_push($singers, $singer);
            }
            $des = $songItem['product_from'];
            if ($des === '' || $des === null) {
                $des = $songItem['nickname'];
            }

            array_push($results, array('song' => $song, 'id' => $id, 'singers' => $singers, 'des' => $des));
        }
        return $results;
    }

    public static function downloadLyric($songId) {
        $url = AumKugouHandler::$siteDownload . $songId;
        $jsonContent = AumKugouHandler::getContent($url, '{"content": ""}');
        $json = json_decode($jsonContent, true);
        $encodeLyric = $json['content'];
        return base64_decode($encodeLyric);
    }
}
