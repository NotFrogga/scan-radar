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
     * Selects users from users table
     * Returns an array of users or null if empty
     */
    function selectAllUsers(){
        $link = databaseConnexion();
        $sQuery = "SELECT `USR_ID`, `USR_MSG_ID` FROM sr_users";
        $query = mysqli_query($link, $sQuery);
        if ($query) {
            return mysqli_fetch_all($query);
        }
        return null;
    }

    /**
     * Selects scans from scan table that are linked to user by Lien_User_Scan
     * Returns an array with data from manga or null if empty
     */
    function selectScansByUserID($_user_id)
    {
        $link = databaseConnexion();
        $sQuery = "SELECT `SCA_NAME`, `SCA_LAST_SCAN`, `SCA_RELEASE_DATE`, `SCA_ID` FROM `sr_scans` s 
                   INNER JOIN `sr_lien_scan_user` lsu ON lsu.LSU_USER_ID = {$_user_id} AND lsu.LSU_SCAN_ID = s.SCA_ID";
        $query = mysqli_query($link, $sQuery);
        if ($query) {
            return mysqli_fetch_all($query);
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
        $query = mysqli_query($link, $iNewItem);
        if ($query) {
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

        $query = mysqli_query($link, $uUserID);
        if ($query) {
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
        $query = mysqli_query($link, $iNewItem);
        if ($query) {
            return true;            
        }
        return false;
    }

    /**
     * Inserts link between user and scan in Lien_Scan_User table
     * $_scan_id is the scan id
     * $_user_id is the user id
     * returns true if insert is successful
     * returns false if insert failed
     */
    function insertLinkUserScan($_scan_id, $_user_id) {
        $link = databaseConnexion();
        $iNewItem = "INSERT INTO `sr_lien_scan_user` (`LSU_SCAN_ID`, `LSU_USER_ID`) VALUES ({$_scan_id}, {$_user_id})
                     ON DUPLICATE KEY UPDATE `LSU_ID` = `LSU_ID`";
        $query = mysqli_query($link, $iNewItem);
        if ($query) {
            return true;
        }
        return false;
    }
?>