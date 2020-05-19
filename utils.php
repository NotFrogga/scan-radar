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
    
    include 'simple_html_dom/simple_html_dom.php';
    $url_mangas = "https://scantrad.net/mangas";

    /**
     *  Gets mangas from a specific URL
     *  returns an array with manga name and manga href string which is used to store the scans
     *  returns null if no mangas were filled.
     */
    function getMangasFromHTML($_url) 
    {
        $html = file_get_html($_url);
        $a = $html->find('a[class=home-manga]');
        $ret = $html->find('a[class=home-manga] div[class=hm-left] div[class=hm-info] div[class=hmi-titre]');
        $array_mangas = array();
        for ($i = 0; $i < max(sizeof($a), sizeof($ret)); $i++) {
            $sub_array = ['name' => $ret[$i]->innertext, 'href' => $a[$i]->href];
            array_push($array_mangas, $sub_array);
        }
        if (sizeof($array_mangas) > 0) {
            return $array_mangas;
        }
        else {
            return null;
        }   
    }

    /**
     *  Writes Manga as a Telegram command in a file.
     *  returns false if nothing is added
     *  returs true if a line is added
     */
    function addMangaToFile($manga)
    {
        $NEW_LINE = "\n";
        $file = 'mangas.txt';

        $manga_name = "/".CleanString($manga["name"]);

        $searchfor = $manga_name;

        // the following line prevents the browser from parsing this as HTML.
        header('Content-Type: text/plain');

        // get the file contents, assuming the file to be readable (and exist)
        $contents = file_get_contents($file);
        // escape special characters in the query
        $pattern = preg_quote($searchfor, '/');
        // finalise the regular expression, matching the whole line
        $pattern = "/^.*$pattern.*\$/m";
        // search, and store all matching occurences in $matches
        if(preg_match_all($pattern, $contents, $matches)){
            return false;
        }
        else{
            file_put_contents($file, $manga_name.$NEW_LINE, FILE_APPEND | LOCK_EX);
            return true;
        }
    }

    /**
     *  Converts a file into an array.
     *  $fileName : string
     *  returns array if success
     *  returs null if failed
     */
    function ConvertFileToArray($fileName) 
    {
        $array = array();
        $handle = fopen($fileName, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) 
            {
                $line = CleanString($line);
                array_push($array, $line);
            }
        
            fclose($handle);
            return $array;
        } 
        else 
        {
            return null;
        } 
    }

    /**
     * Cleans string
     * Returns string
     */
    function CleanString($string)
    {
        $string = str_replace("\n", '', $string);
        $string = str_replace("\r", '', $string);
        $string = str_replace('.', '', $string);
        $string = str_replace(':', '', $string);
        $string = str_replace('-', '', $string);
        $string = str_replace(' ', '', $string);

        return $string;
    }
?>