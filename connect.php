<?php
    /**
     * Connection to MySQL in Xampp
     */
    function databaseConnexion()
    {
        include 'variables.php';
        $link = mysqli_connect($dbhost, $user, $password, $db);
        if (!$link) {
            echo ("Error : unable to connect to database" . PHP_EOL);
            exit;
        }
        return $link;
    }

    /**
     * Selects from database mangas
     * Returns an array with data from manga or null if empty
     */
    function selectMangas()
    {
        $link = databaseConnexion();
        $sQuery = "SELECT `SCA_NAME`, `SCA_LAST_SCAN`, `SCA_RELEASE_DATE`, `SCA_ID` FROM `sr_scans`";
        $result = mysqli_query($link, $sQuery);
        if ($result) {
            return mysqli_fetch_all($result);
        }
        return null;
    }

    /**
     * Updates database manga table and flux_rss table.
     * Receives an xml item in entry.
     */
    function addNewScanToDb($manga_id, $_scan_number, $_scan_date)
    {
        $link = databaseConnexion();
        $iNewItem =
            "UPDATE `sr_scans`
            SET
            `SCA_LAST_SCAN` = '{$_scan_number}',
            `SCA_RELEASE_DATE` = '{$_scan_date}'
            WHERE `SCA_ID` = '{$manga_id}'";
        $result = mysqli_query($link, $iNewItem);
        if ($result) {
            return true;
        }
        return false;
    }

    function insertUserID($_user_msg_ID)
    {
        $link = databaseConnexion();
        $uUserID =
        "INSERT INTO `users`
        (`USR_MSG_ID`)
        VALUES
        ({$_user_msg_ID})
        ";

        $result = mysqli_query($link, $uUserID);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * Inserts manga in manga table
     * $_array contains 'name' and 'href'
     * returns true if insert is successful
     * returns false if insert failed
     */
    function addMangatoDB($_array) {
        $link = databaseConnexion();
        $_manga_name = $_array["name"];
        $iNewItem = "INSERT INTO `sr_mangas` (`MAN_NAME`) VALUES ('{$_manga_name}')
                     ON DUPLICATE KEY UPDATE `MAN_ID` = `MAN_ID`";
        $result = mysqli_query($link, $iNewItem);
        if ($result) {
            return true;            
        }
        return false;
    }
?>