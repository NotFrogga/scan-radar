<?php

    /**
     * CONSTANTS
     */
    $FLUX_RSS_TABLE = "sr_flux_rss";
    $LIEN_SCAN_USER_TABLE = "sr_lien_scan_user";
    $MANGAS_TABLE = "sr_mangas";
    $SCANS_TABLE = "sr_scans";
    $USERS_TABLE = "sr_users";

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
    function selectAllUsers()
    {
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
     * Inserts manga in manga table and scan linked to that manga in scans table
     * $_array contains 'name' and 'href'
     * returns true if inserts are successful
     * returns false if inserts failed
     */
    function addMangatoDB($_array) 
    {
        $link = databaseConnexion();
        $_manga_name = $_array["name"];
        $_command_name = "/".CleanString($_array["name"]);
        $iNewManga = "INSERT INTO `sr_mangas` (`MAN_NAME`, `MAN_COMMAND_NAME`) VALUES ('{$_manga_name}', '{$_command_name}')
                     ON DUPLICATE KEY UPDATE `MAN_ID` = `MAN_ID`";
        $iMangaQuery = mysqli_query($link, $iNewManga);

        $iNewScan = "INSERT INTO `sr_scans` 
                    (SCA_NAME,
                    SCA_FK_MAN_ID,
                    SCA_LAST_SCAN,
                    SCA_RELEASE_DATE) 
                    VALUES ('{$_manga_name}', 
                    (SELECT MAN_ID FROM `sr_mangas` WHERE MAN_NAME = '{$_manga_name}'),
                    0,
                    '1999-01-01 00:00:00'
                    )
        ON DUPLICATE KEY UPDATE `SCA_ID` = `SCA_ID`";
        $iScanQuery = mysqli_query($link, $iNewScan);
        mysqli_close($link);
        if ($iMangaQuery && $iScanQuery) {
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
    function insertLinkUserScan($_scan_id, $_user_id) 
    {
        $link = databaseConnexion();
        $iNewItem = "INSERT INTO `sr_lien_scan_user` (`LSU_SCAN_ID`, `LSU_USER_ID`) VALUES ({$_scan_id}, {$_user_id})
                     ON DUPLICATE KEY UPDATE `LSU_ID` = `LSU_ID`";
        $query = mysqli_query($link, $iNewItem);
        if ($query) {
            return true;
        }
        return false;
    }

    
    /**
     * Check if string exists in database
     * return true if exists
     * false if not
     */
    function Exists($_manga_command_name)
    {
        $link = databaseConnexion();
        $string = mysqli_real_escape_string($link, $_manga_command_name);
        $sQuery = "SELECT `MAN_ID` FROM `sr_mangas` WHERE `MAN_COMMAND_NAME` = '{$string}'";
        $query = mysqli_query($link, $sQuery);
        mysqli_close($link);
        if ($query)
        {
            return true;
        }
        else 
        {
            return false;
        }
    }

    /**
     * Insert a link between a user and scan
     * return true if insert is successful
     * false if not
     */
    function InsertMangaToUser($_manga_command_name, $chat_id)
    {
        try {
            $link = databaseConnexion();
            $_command = mysqli_real_escape_string($link, $_manga_command_name);
            $sQuery = "SELECT `SCA_ID` as ScanId
                       FROM `sr_scans` s
                       INNER JOIN `sr_mangas` m ON `MAN_COMMAND_NAME` = '{$_command}' AND m.MAN_ID = s.SCA_FK_MAN_ID
                       WHERE `SCA_ID` = `SCA_ID`";
            $selectQuery = mysqli_query($link, $sQuery);
            $queryArray = mysqli_fetch_all($selectQuery);
            $scan_id = $selectQuery["ScanId"];
            $iQuery = "INSERT INTO `sr_lien_scan_user` (`LSU_SCAN_ID`, `LSU_USER_ID`) VALUES ({$scan_id}, {$chat_id}) ON DUPLICATE KEY UPDATE `LSU_ID` = `LSU_ID`";
            $insertQuery = mysqli_query($link, $iQuery);
            mysqli_close($link);
    
            if ($selectQuery && $insertQuery)
            {
                return true;
            }
            else 
            {
                return false;
            }
        }
        catch (Exception $e)
        {
            echo $e;
        }
        
    }
?>