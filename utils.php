<?php
/**
     * Returns RSS copyright data which reprensents website adress
     */
    function getRssName($xml_object)
    {
        return $xml_object->copyright;
    }

    /**
     * Formats time stamp to DateTime object
     * Returns formated DateTime
     */
    function convertToDateTime($timeStamp)
    {
        $date = new DateTime();
        $date->setTimestamp($timeStamp);
        return $date->format("Y-m-d H:i:s");
    }

    /**
     * Returns last item time
     */
    function getListItemDate($xml_object)
    {
        return $xml_object->channel[0]->pubDate;
    }

    /**
     * Finds if a string $_string contains the manga name $_mangaName
     * Returns true if it contains it, false if not.
     */
    function mangasToSearch($_string, $_mangaName)
    {
        if (strpos($_string, $_mangaName)) {
            return true;
        }
        return false;
    }
    
    /**
     * Finds scan number in rss feed xml item.
     */
    function findScanNumber($_url)
    {
        $parsed_url = parse_url($_url, PHP_URL_PATH);
        $output = explode("/", $parsed_url);
        return end($output);
    }

    /**
     * Creates an array that will feed the new manga table update query.
     */
    function createNewScan($_manga_id, $_manga_name, $_item_scan_number, $_url, $_item_date)
    {
        return array(
            "id" => $_manga_id,
            "name" => $_manga_name,
            "scan_number" => $_item_scan_number,
            "url" => $_url,
            "date" => $_item_date
        );
    }
?>