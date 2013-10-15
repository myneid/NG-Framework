<?php

/**
 * NG Framework
 * Version 0.1 Beta
 * Copyright (c) 2012, Nick Gejadze
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), 
 * to deal in the Software without restriction, including without limitation 
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace NG;

/**
 * Youtube
 * @package NG
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2012, Nick Gejadze
 */
class Youtube {

    const youtubeApiVersion = "2";

    private $developerKey;
    private $defaultParams = array(
        "v" => self::youtubeApiVersion,
        "alt" => "json",
        "start-index" => "1",
        "max-results" => "25",
        "lr" => "en",
        "orderby" => "relevance",
        "safeSearch" => "moderate",
        "q" => ""
    );
    private $search = "http://gdata.youtube.com/feeds/api/videos?";
    //private $searchChannel = "http://gdata.youtube.com/feeds/api/channels?";
    private $single = "http://gdata.youtube.com/feeds/api/videos/[VIDEOID]?";
    private $related = "http://gdata.youtube.com/feeds/api/videos/[VIDEOID]/related?";
    private $topRated = "http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?";
    private $topFavorites = "http://gdata.youtube.com/feeds/api/standardfeeds/top_favorites?";
    private $mostViewed = "http://gdata.youtube.com/feeds/api/standardfeeds/most_viewed?";
    private $mostShared = "http://gdata.youtube.com/feeds/api/standardfeeds/most_shared?";
    private $mostPopular = "http://gdata.youtube.com/feeds/api/standardfeeds/most_popular?";
    private $mostRecent = "http://gdata.youtube.com/feeds/api/standardfeeds/most_recent?";
    private $mostDiscussed = "http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed?";
    private $mostResponded = "http://gdata.youtube.com/feeds/api/standardfeeds/most_responded?";
    private $recentlyFeatured = "http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?";
    private $tranding = "http://gdata.youtube.com/feeds/api/standardfeeds/on_the_web?";
    private $result = array();

    public function __construct($developerKey = null) {
        if (isset($developerKey) and !empty($developerKey)):
            $this->setKey($developerKey);
        endif;
        return $this;
    }

    public function setKey($developerKey) {
        $this->defaultParams['key'] = $developerKey;
        return $this;
    }

    public function setStart($start) {
        if (is_numeric($start)):
            $this->defaultParams['start-index'] = $start;
        endif;
        return $this;
    }

    public function setLimit($limit) {
        if (is_numeric($limit)):
            $this->defaultParams['max-results'] = $limit;
        endif;
        return $this;
    }

    /* relevance published viewCount */

    public function setOrderBy($order) {
        if (isset($order) and in_array($order, array("relevance", "published", "viewCount"))):
            $this->defaultParams['orderby'] = $order;
        endif;
        return $this;
    }

    public function setSafeSearch($safeSearch) {
        if (isset($safeSearch) and in_array($safeSearch, array("none", "moderate", "strict"))):
            $this->defaultParams['safeSearch'] = $safeSearch;
        endif;
        return $this;
    }

    /* ISO 639-1 */

    public function setLanguage($languageCode) {
        if (strlen($languageCode) == 2):
            $this->defaultParams['lr'] = $languageCode;
        endif;
        return $this;
    }

    public function search($query = null) {
        $this->defaultParams['q'] = urlencode($query);
        return $this->request($this->search);
    }

    /* public function searchChannel($query = null) {
      $this->defaultParams['q'] = urlencode($query);
      return $this->getPageAsArray($this->searchChannel);
      } */

    public function getRelated($videoID) {
        $url = str_replace("[VIDEOID]", $videoID, $this->related);
        return $this->request($url);
    }

    public function getTopRated() {
        return $this->request($this->topRated);
    }

    public function getTopFavorites() {
        return $this->request($this->topFavorites);
    }

    public function getMostViewed() {
        return $this->request($this->mostViewed);
    }

    public function getMostShared() {
        return $this->request($this->mostShared);
    }

    public function getMostPopular() {
        return $this->request($this->mostPopular);
    }

    public function getMostRecent() {
        return $this->request($this->mostRecent);
    }

    public function getMostDiscussed() {
        return $this->request($this->mostDiscussed);
    }

    public function getMostResponded() {
        return $this->request($this->mostResponded);
    }

    public function getRecentlyFeatured() {
        return $this->request($this->recentlyFeatured);
    }

    public function getTranding() {
        return $this->request($this->tranding);
    }

    private function request($url) {
        $url .= implode('&amp;', array_map(function($key, $val) {
                            return urlencode($key) . '=' . urlencode($val);
                        }, array_keys($this->defaultParams), $this->defaultParams)
        );
        $this->httpclient = new \NG\Httpclient();
        $this->httpclient->setUri($url);
        $request = $this->httpclient->request();
        return $this->setData(json_decode($request['content'], true));
    }

    public function getResultAsArray() {
        return $this->result;
    }

    public function getEntry($videoID) {
        if (isset($videoID) and isset($this->result[$videoID])):
            return $this->result[$videoID];
        elseif (isset($videoID)):
            $url = str_replace("[VIDEOID]", $videoID, $this->single);
            /* Temporary fix for Gdata Error */
            unset($this->defaultParams['start-index']);
            unset($this->defaultParams['max-results']);
            $request = $this->request($url);
            if (isset($this->result[$videoID])):
                return $this->result[$videoID];
            endif;
        endif;
        return false;
    }

    public function getTitle($videoID) {
        return $this->getSingelElement("title", $videoID);
    }

    public function getPublishedDate($videoID) {
        return $this->getSingelElement("published", $videoID);
    }

    public function getUpdatedDate($videoID) {
        return $this->getSingelElement("contentType", $videoID);
    }

    public function getContentSrc($videoID) {
        return $this->getSingelElement("contentSrc", $videoID);
    }

    public function getAuthor($videoID) {
        return $this->getSingelElement("author", $videoID);
    }

    public function getLicense($videoID) {
        return $this->getSingelElement("license", $videoID);
    }

    public function getPlayer($videoID) {
        return $this->getSingelElement("player", $videoID);
    }

    public function getDuration($videoID) {
        return $this->getSingelElement("duration", $videoID);
    }

    public function getDescription($videoID) {
        return $this->getSingelElement("description", $videoID);
    }

    /* returns array */

    public function getContent($videoID) {
        return $this->getSingelElement("mediaContent", $videoID);
    }

    /* returns array */

    public function getThumbnails($videoID) {
        return $this->getSingelElement("thumbnail", $videoID);
    }

    /* returns array */

    public function getKeywords($videoID) {
        return $this->getSingelElement("keywords", $videoID);
    }

    public function getCommentStatus($videoID) {
        return $this->getSingelElement("comment", $videoID);
    }

    public function getCommentVoteStatus($videoID) {
        return $this->getSingelElement("commentVote", $videoID);
    }

    public function getVideoRespondStatus($videoID) {
        return $this->getSingelElement("videoRespond", $videoID);
    }

    public function getRateStatus($videoID) {
        return $this->getSingelElement("rate", $videoID);
    }

    public function getEmbedStatus($videoID) {
        return $this->getSingelElement("embed", $videoID);
    }

    public function getListStatus($videoID) {
        return $this->getSingelElement("list", $videoID);
    }

    public function getAutoPlayStatus($videoID) {
        return $this->getSingelElement("autoPlay", $videoID);
    }

    public function getSyndicateStatus($videoID) {
        return $this->getSingelElement("syndicate", $videoID);
    }

    /* returns array */

    public function getCategories($videoID) {
        return $this->getSingelElement("categories", $videoID);
    }

    public function getLikes($videoID) {
        return $this->getSingelElement("likes", $videoID);
    }

    public function getDislikes($videoID) {
        return $this->getSingelElement("dislikes", $videoID);
    }

    public function getViewCount($videoID) {
        return $this->getSingelElement("viewCount", $videoID);
    }

    public function getFavoriteCount($videoID) {
        return $this->getSingelElement("favoriteCount", $videoID);
    }

    public function getCommentsUrl($videoID) {
        return $this->getSingelElement("comments", $videoID);
    }

    public function getCommentsCount($videoID) {
        return $this->getSingelElement("commentsCount", $videoID);
    }

    public function getCredit($videoID) {
        return $this->getSingelElement("credit", $videoID);
    }

    public function getRating($videoID) {
        return $this->getSingelElement("rating", $videoID);
    }

    public function getRatingMax($videoID) {
        return $this->getSingelElement("ratingMax", $videoID);
    }

    public function getRatingMin($videoID) {
        return $this->getSingelElement("ratingMin", $videoID);
    }

    public function getNumRaters($videoID) {
        return $this->getSingelElement("numRaters", $videoID);
    }

    private function getSingelElement($element, $videoID) {
        if (isset($element) and isset($videoID) and isset($this->result[$videoID])):
            return isset($this->result[$videoID][$element]) ? $this->result[$videoID][$element] : false;
        elseif (isset($element) and isset($videoID)):
            if ($this->getEntry($videoID)):
                return isset($this->result[$videoID][$element]) ? $this->result[$videoID][$element] : false;
            endif;
        endif;
        return false;
    }

    private function setData($data) {
        if (isset($data['feed']['entry'])):
            foreach ($data['feed']['entry'] as $key => $entry):
                $this->setEntryData($entry);
                $this->setEntryDescription($data['entry']);
                $this->setEntryKeywords($entry);
                $this->setEntryThumbnails($entry);
                $this->setEntryContent($entry);
                $this->setEntryAccessControl($entry);
                $this->setEntryCategories($entry);
                $this->setEntryLikes($entry);
                $this->setEntryDislikes($entry);
                $this->setEntryViewCount($entry);
                $this->setEntryFavoriteCount($entry);
                $this->setEntryComments($entry);
                $this->setEntryCommentsCount($entry);
                $this->setEntryCredit($entry);
                $this->setEntryAspectRation($entry);
                $this->setEntryRating($entry);
            endforeach;
            return array_keys($this->result);
        elseif (isset($data['entry'])):
            $this->setEntryData($data['entry']);
            $this->setEntryDescription($data['entry']);
            $this->setEntryKeywords($data['entry']);
            $this->setEntryThumbnails($data['entry']);
            $this->setEntryContent($data['entry']);
            $this->setEntryAccessControl($data['entry']);
            $this->setEntryCategories($data['entry']);
            $this->setEntryLikes($data['entry']);
            $this->setEntryDislikes($data['entry']);
            $this->setEntryViewCount($data['entry']);
            $this->setEntryFavoriteCount($data['entry']);
            $this->setEntryComments($data['entry']);
            $this->setEntryCommentsCount($data['entry']);
            $this->setEntryCredit($data['entry']);
            $this->setEntryAspectRation($data['entry']);
            $this->setEntryRating($data['entry']);
            return array_keys($this->result);
        endif;
        return false;
    }

    private function setEntryData($entry) {
        if (isset($entry) and isset($entry['content']['src'])):
            $this->result[$entry['media$group']['yt$videoid']['$t']] = array(
                "videoID" => $entry['media$group']['yt$videoid']['$t'],
                "published" => $entry['published']['$t'],
                "updated" => $entry['updated']['$t'],
                "title" => $entry['title']['$t'],
                "contentType" => $entry['content']['type'],
                "contentSrc" => $entry['content']['src'],
                "author" => array(
                    "userID" => $entry['author'][0]['yt$userId']['$t'],
                    "name" => $entry['author'][0]['name']['$t'],
                    "uri" => $entry['author'][0]['uri']['$t']
                ),
                "license" => $entry['media$group']['media$license']['$t'],
                "player" => $entry['media$group']['media$player']['url'],
                "duration" => $entry['media$group']['yt$duration']['seconds']
            );
            return true;
        endif;
        return false;
    }

    private function setEntryDescription($entry) {
        if (isset($entry['media$group']['media$description']['$t'])):
            $this->result[$entry['media$group']['yt$videoid']['$t']]['description'] = $entry['media$group']['media$description']['$t'];
            return true;
        endif;
        return false;
    }

    private function setEntryContent($entry) {
        if (isset($entry['media$group']['media$content']) and is_array($entry['media$group']['media$content'])):
            foreach ($entry['media$group']['media$content'] as $mediaContent):
                switch ($mediaContent['yt$format']):
                    case 5:
                        $this->result[$entry['media$group']['yt$videoid']['$t']]['mediaContent']['embeddablePlayer'] = array(
                            "url" => $mediaContent['url'],
                            "type" => $mediaContent['type'],
                            "medium" => $mediaContent['medium'],
                            "duration" => $mediaContent['duration']
                        );
                        break;
                    case 1:
                        $this->result[$entry['media$group']['yt$videoid']['$t']]['mediaContent']['mobileVideoh263'] = array(
                            "url" => $mediaContent['url'],
                            "type" => $mediaContent['type'],
                            "medium" => $mediaContent['medium'],
                            "duration" => $mediaContent['duration']
                        );
                        break;
                    case 6:
                        $this->result[$entry['media$group']['yt$videoid']['$t']]['mediaContent']['mobileVideoMpeg4'] = array(
                            "url" => $mediaContent['url'],
                            "type" => $mediaContent['type'],
                            "medium" => $mediaContent['medium'],
                            "duration" => $mediaContent['duration']
                        );
                        break;
                endswitch;
            endforeach;
            return true;
        endif;
        return false;
    }

    private function setEntryThumbnails($entry) {
        if (isset($entry['media$group']['media$thumbnail']) and is_array($entry['media$group']['media$thumbnail'])):
            foreach ($entry['media$group']['media$thumbnail'] as $thumbnail):
                $this->result[$entry['media$group']['yt$videoid']['$t']]['thumbnail'][$thumbnail['yt$name']] = array(
                    "url" => $thumbnail['url'],
                    "height" => $thumbnail['height'],
                    "width" => $thumbnail['width']
                );
                if (isset($thumbnail['time']) and !empty($thumbnail['time'])):
                    $this->result[$entry['media$group']['yt$videoid']['$t']]['thumbnail'][$thumbnail['yt$name']]['time'] = $thumbnail['time'];
                endif;
            endforeach;
            return true;
        endif;
        return false;
    }

    private function setEntryKeywords($entry) {
        if (isset($entry['media$group']['media$keywords']) and !empty($entry['media$group']['media$keywords'])):
            foreach ($entry['media$group']['media$keywords'] as $keyword):
                $this->result[$entry['media$group']['yt$videoid']['$t']]['mediaContent']['keywords'][] = $keyword;
            endforeach;
            return true;
        endif;
        return false;
    }

    private function setEntryAccessControl($entry) {
        if (isset($entry['yt$accessControl']) and is_array($entry['yt$accessControl'])):
            foreach ($entry['yt$accessControl'] as $accessControl):
                $this->result[$entry['media$group']['yt$videoid']['$t']][$accessControl['action']] = $accessControl['permission'];
            endforeach;
            return true;
        endif;
        return false;
    }

    private function setEntryCategories($entry) {
        if (isset($entry['category']) and is_array($entry['category'])):
            foreach ($entry['category'] as $category):
                if (isset($category['term']) AND !empty($category['term']) AND isset($category['label']) AND !empty($category['label'])):
                    $this->result[$entry['media$group']['yt$videoid']['$t']]['categories'][$category['term']] = $category['label'];
                endif;
            endforeach;
            return true;
        endif;
        return false;
    }

    private function setEntryLikes($entry) {
        if (isset($entry['yt$rating']['numLikes']) and is_numeric($entry['yt$rating']['numLikes'])):
            $this->result[$entry['media$group']['yt$videoid']['$t']]['likes'] = $entry['yt$rating']['numLikes'];
            return true;
        endif;
        return false;
    }

    private function setEntryDislikes($entry) {
        if (isset($entry['yt$rating']['numDislikes']) and is_numeric($entry['yt$rating']['numDislikes'])):
            $this->result[$entry['media$group']['yt$videoid']['$t']]['dislikes'] = $entry['yt$rating']['numDislikes'];
            return true;
        endif;
        return false;
    }

    private function setEntryViewCount($entry) {
        if (isset($entry['yt$statistics']['viewCount']) and is_numeric($entry['yt$statistics']['viewCount'])):
            $this->result[$entry['media$group']['yt$videoid']['$t']]['viewCount'] = $entry['yt$statistics']['viewCount'];
            return true;
        endif;
        return false;
    }

    private function setEntryFavoriteCount($entry) {
        if (isset($entry['yt$statistics']['favoriteCount']) and is_numeric($entry['yt$statistics']['favoriteCount'])):
            $this->result[$entry['media$group']['yt$videoid']['$t']]['favoriteCount'] = $entry['yt$statistics']['favoriteCount'];
            return true;
        endif;
        return false;
    }

    private function setEntryComments($entry) {
        if (isset($entry['gd$comments']['gd$feedLink']['href'])):
            $this->result[$entry['media$group']['yt$videoid']['$t']]['comments'] = $entry['gd$comments']['gd$feedLink']['href'];
            return true;
        endif;
        return false;
    }

    private function setEntryCommentsCount($entry) {
        if (isset($entry['gd$comments']['gd$feedLink']['countHint'])):
            $this->result[$entry['media$group']['yt$videoid']['$t']]['commentsCount'] = $entry['gd$comments']['gd$feedLink']['countHint'];
            return true;
        endif;
        return false;
    }

    private function setEntryCredit($entry) {
        if (isset($entry['media$group']['media$credit'][0]['yt$display'])):
            $this->result[$entry['media$group']['yt$videoid']['$t']]['credit'] = $entry['media$group']['media$credit'][0]['yt$display'];
            return true;
        endif;
        return false;
    }

    private function setEntryAspectRation($entry) {
        if (isset($entry['media$group']['yt$aspectRatio']['$t'])):
            $this->result[$entry['media$group']['yt$videoid']['$t']]['aspectRation'] = $entry['media$group']['yt$aspectRatio']['$t'];
            return true;
        endif;
        return false;
    }

    private function setEntryRating($entry) {
        if (isset($entry['gd$rating']['average']) and isset($entry['gd$rating']['numRaters'])):
            $this->result[$entry['media$group']['yt$videoid']['$t']]['rating'] = $entry['gd$rating']['average'];
            $this->result[$entry['media$group']['yt$videoid']['$t']]['ratingMax'] = $entry['gd$rating']['max'];
            $this->result[$entry['media$group']['yt$videoid']['$t']]['ratingMin'] = $entry['gd$rating']['min'];
            $this->result[$entry['media$group']['yt$videoid']['$t']]['numRaters'] = $entry['gd$rating']['numRaters'];
            return true;
        endif;
        return false;
    }

}
