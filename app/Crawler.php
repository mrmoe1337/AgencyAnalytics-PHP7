<?php

namespace app\Crawler;

use DOMDocument;
use DOMNodeList;

class Crawler
{
    /**
     * @var $pages counts the pages crawled
     * @var $depth number of pages that you want to crawl including the initial page
     * @var $urlStorageInternal storage of the urls that are internal
     * @var $urlStorageExternal storage of the urls that are external
     * @var $imgStorage storage of images that are unique
     * @var $httpStatusStorage storage of the HTTP status codes
     * @var $avgLoadTime stores load times from each request
     * @var $avgTitleLength stores average title length
     * @var $avgWordCount stores the average word count of a page
     * @var $mainURL main url of the website
     */

    private int $pages = 0;
    private int $depth;
    private array $urlStorageInternal = [];
    private array $urlStorageExternal = [];
    private array $imgStorage = [];
    private array $httpStatusStorage = [];
    private array $avgLoadTime = [];
    private array $avgTitleLength = [];
    private array $avgWordCount = [];
    private string $mainURL;

    /**
     * our constructor
     * @param string $url
     * @param int $depth
     */
    public function __construct(string $url, int $depth)
    {
        $this->mainURL = $url;
        $this->depth = $depth;
    }

    /**
     * Creates the fancy report
     * @return Crawler
     */
    public function createReport(): Crawler
    {
        $filePath = "./views/page-report.php";
        $output = NULL;
        if (file_exists($filePath)) {

            $eq = (fn($avgTitleLength) => array_sum($avgTitleLength)/count($avgTitleLength));

            $vars = [
                'pages' => $this->pages,
                'imgStorage' => count($this->imgStorage),
                'urlStorageInternal' => count($this->urlStorageInternal),
                'urlStorageExternal' => count($this->urlStorageExternal),
                'avgLoadTime' => array_sum($this->avgLoadTime) / count($this->avgLoadTime),
                'avgWordCount' => array_sum($this->avgWordCount) / count($this->avgWordCount),
                'avgTitleLength' => $eq($this->avgTitleLength),
                'httpStatusStorage' => $this->httpStatusStorage
            ];
            extract($vars);
            ob_start();
            include $filePath;
            $output = ob_get_clean();
        }
        echo $output;
        return $this;
    }

    /**
     * Triggers the crawling of the pages
     * @return Crawler
     */
    public function crawlPage(): Crawler
    {
        $url = $this->mainURL;
        $data = $this->getHTTPRequest($url, false);

        $getDocument = new DOMDocument();
        @$getDocument->loadHTML($data['content']);

        $anchors = $getDocument->getElementsByTagName('a');
        $paths = [];

        foreach ($anchors as $element) {
            $href = $element->getAttribute('href');
            if (count($paths) != $this->depth && $this->isInternal($href)) {
                $paths[] = $href;
            }
        }

        $this->fetchInfo($url, $paths);
        return $this;
    }

    /**
     * Fetches information for paths
     * @param string $url
     * @param array $paths
     * @return void
     */
    private function fetchInfo(string $url, array $paths): void
    {
        if (!empty($paths)) {
            foreach ($paths as $item) {
                $data = $this->getHTTPRequest($url . $item, true);

                $getDocument = new DOMDocument();
                @$getDocument->loadHTML($data['content']);

                $this->setHTTPStatuses(array(
                    'url' => $url . $item,
                    'status' => $data['status'],
                    'loadTime' => $data['totalTime']
                ));

                // Insert Load Times
                $this->setLoadTimes($data['totalTime']);

                // Count words
                $this->fetchWordCount($url . $item);

                // scan title tags
                $this->scanTitles($getDocument->getElementsByTagName('title'));

                // Scan anchor tags
                $this->scanAnchors($getDocument->getElementsByTagName('a'));

                // Scan image tags
                $this->scanImages($getDocument->getElementsByTagName('img'));

                // Crawled pages
                $this->pages++;
            }
        }
    }

    /**
     * Sets HTTP status for all the pages
     * @param array $data
     * @return void
     */
    private function setHTTPStatuses(array $data): void
    {
        $this->httpStatusStorage[] = $data;
    }

    /**
     * Sets Load times so that we can get an average later on
     * @param float $totalTime
     * @return void
     */
    private function setLoadTimes(float $totalTime): void
    {
        $this->avgLoadTime[] = $totalTime;
    }

    /**
     * Scans for the titles and adds them to the array
     * @param DOMNodeList $titles
     * @return void
     */
    private function scanTitles(DOMNodeList $titles): void
    {
        $this->avgTitleLength[] = strlen($titles[0]->nodeValue);
    }

    /**
     * Scans for anchor tags and finds out if they're internal or external
     * @param DOMNodeList $anchors
     * @return void
     */
    private function scanAnchors(DOMNodeList $anchors): void
    {
        foreach ($anchors as $element) {
            $href = $element->getAttribute('href');
            if ($this->isInternal($href)) {
                if (!in_array($href, $this->urlStorageInternal)) {
                    $this->urlStorageInternal[] = $href;
                }
            } else {
                $parse = parse_url($href);
                if (!in_array($href, $this->urlStorageExternal) && isset($parse['host'])) {
                    // counting subdomains as external
                    $this->urlStorageExternal[] = $href;
                } elseif (!in_array($href, $this->urlStorageExternal)) {
                    // nice trick with the href="#main" it ends up here :)
                }
            }
        }
    }

    /**
     * Scans for unique images
     * @param DOMNodeList $images
     * @return void
     */
    private function scanImages(DOMNodeList $images): void
    {
        foreach ($images as $element) {
            $src = $element->getAttribute('data-src');
            if (!empty($src)) {
                if (!in_array($src, $this->imgStorage)) {
                    $this->imgStorage[] = $src;
                }
            }
        }
    }

    /**
     * fetches word count inside the body tag
     * @param string $url
     * @return void
     */
    private function fetchWordCount(string $url): void
    {
        libxml_use_internal_errors(true);
        $html = $this->getHTTPRequest($url, false);

        $getDocument = new DOMDocument();
        $getDocument->preserveWhiteSpace = false;
        $getDocument->loadHTML($html['content']);

        $optionals = ['iframe']; //just to show unpacking inside an array :)
        $removeTags = ['script', 'style', ...$optionals, 'link', 'script'];

        foreach ($removeTags as $tag) {
            $element = $getDocument->getElementsByTagName($tag);
            foreach ($element as $item) {
                $item->parentNode->removeChild($item);
            }
        }

        $getDocument->saveHTML();
        $html = $getDocument->getElementsByTagName('body')->item(0)->nodeValue;
        $count = str_word_count(preg_replace("/\n\t+/", "", $html));
        $this->avgWordCount[] = $count;
    }

    /**
     * Checks if an url is internal or external in the source code
     * @param string $url
     * @return bool
     */
    private function isInternal(string $url): bool
    {
        $parse = parse_url($url);
        if (isset($parse['scheme']) || isset($parse['host'])) {
            $parse_main = parse_url($this->mainURL);
            if ($parse['host'] === $parse_main['host']) {
                return true;
            }
            return false;
        }
        if (isset($parse['path'])) {
            return true;
        }
        return false;
    }

    /**
     * Fires a cURL request
     * @param string $url
     * @param string $headers
     * @return array
     */
    private function getHTTPRequest(string $url, string $headers): array
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if ($headers) {
            curl_setopt($curl, CURLOPT_HEADER, true);
            $resp = curl_exec($curl);
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $totalTime = curl_getinfo($curl, CURLINFO_TOTAL_TIME);
            curl_close($curl);
            return [
                'status' => $httpStatus,
                'totalTime' => floor($totalTime * 1000),
                'content' => $resp
            ];
        } else {
            $resp = curl_exec($curl);
            curl_close($curl);
            return [
                'content' => $resp
            ];
        }
    }
}
