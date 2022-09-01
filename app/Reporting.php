<?php

namespace app\Crawler;

class Reporting extends Crawler {
    /**
     * @return void
     */
    public function createReport() : void
    {
        echo "Pages Crawled: " .$this->pages."<br/>";
        echo "Unique images: " .count($this->imgStorage)."<br/>";
        echo "Unique Internal Links: " .count($this->urlStorageInternal)."<br/>";
        echo "Unique External Links: " .count($this->urlStorageExternal)."<br/>";
        echo "Average Load Time: " .array_sum($this->avgLoadTime)/count($this->avgLoadTime)."<br/>";
        echo "Average Word Count: " .array_sum($this->avgWordCount)/count($this->avgWordCount)."<br/>";
        echo "Average Title Length: " .array_sum($this->avgTitleLength)/count($this->avgTitleLength);

        echo "<h3>Crawled Pages</h3>";
        echo "<table style='margin:10px 0 0 0;' border='1'>";
        echo "<thead><tr><td>URL</td><td>HTTP Status</td></tr></thead>";
        foreach ($this->httpStatusStorage as $item) {
            echo "<tr><td>".$item['url']. "</td> <td>" .$item['status'] . "</td></tr>";
        }
        echo "</table>";
    }
}