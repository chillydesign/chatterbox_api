<?php


include('connect.php');
include('functions.php');


function klxc_add_migration($query) {
    global $conn;
        try {
            $migration_query = $conn->prepare($query);
            $migration_query->execute();
            unset($conn);
            return true;

        } catch(PDOException $err) {
            return false;

        };
}


$add_approved_to_usrs = "ALTER TABLE `chusers` ADD `is_approved` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_an_admin`;  ";
if (klxc_add_migration($add_approved_to_usrs)) {
    echo 'added add_approved_to_usrs';
} else {
    echo 'error add_approved_to_usrs';
};
// $add_file_to_messa = "ALTER TABLE `messages` ADD `file` VARCHAR(511) NOT NULL AFTER `conversation_id`;  ";
// if (klxc_add_migration($add_file_to_messa)) {
//     echo 'added add_file_to_messa';
// } else {
//     echo 'error add_file_to_messa';
// };
// $add_is_ad_to_usersges = "ALTER TABLE `chusers` ADD `is_an_admin` TINYINT(1) NOT NULL DEFAULT '0' AFTER `username`; ";
// if (klxc_add_migration($add_is_ad_to_usersges)) {
//     echo 'added add_is_ad_to_usersges';
// } else {
//     echo 'error add_is_ad_to_usersges';
// };





?>