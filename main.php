<?php
    /**
     * Connexion to database and SQL queries
     */
    include 'connect.php';
    /**
     * Functions that facilitate execution
     */
    include 'utils.php';
    /**
     * HTTP REST Telegram API
     */
    include 'message.php';

    include 'variables.php';
    
    /**
     * Access to RSS stream and parsing it in an array
     */
    $url_rss = 'https://scantrad.net/rss/';
    $xml = simplexml_load_file($url_rss) or die("Cannot connect to " . $url_rss);


    $lastItem = $xml->channel->item[0];
    $lastpubDateToString = strtotime($lastItem->pubDate->__toString());
    $lastpubDateDateTime = convertToDateTime($lastpubDateToString);

    /**
     * Checks if if there are new mangas in $xml compared to $_arrayMangas data
     */
    function searchNewScans($_xml, $_mangas)
    {
        foreach ($_xml->channel->item as $item) {
            foreach ($_mangas as $manga) {
                $newScans = array();
                $manga_name = $manga[0];
                $manga_last_scan  = $manga[1];
                $manga_date = $manga[2];
                $manga_id = $manga[3];
                $item_scan_number = findScanNumber($item->link);
                $item_scan_date = convertToDateTime(strtotime($item->pubDate->__toString()));
                if (mangasToSearch($item->title, $manga_name)) {
                    if ($item_scan_date > $manga_date && $item_scan_number > $manga_last_scan) {
                        $newScan = createNewScan($manga_id, $manga_name, $item_scan_number, $item->link, $item_scan_date);
                        array_push($newScans, $newScan);
                    }
                }
                if (count($newScans) > 0) {
                    for ($i = count($newScans) - 1; $i >= 0; $i--) {
                        $newScan = $newScans[$i];
                        messageNewScan($newScan["name"], $newScan["scan_number"], $newScan["date"], $newScan["url"]);
                    }
                    $lastestScan = $newScans[0];
                    addNewScanToDb($lastestScan["id"], $lastestScan["scan_number"], $lastestScan["date"]);
                }
            }
        }
    }

/**
* 
* PHP Executions methods
* 
*/
$mangas = selectMangas();
searchNewScans($xml, $mangas);
?>