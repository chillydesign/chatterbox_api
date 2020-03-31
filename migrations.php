<?php


include('connect.php');
include('functions.php');


// function klxc_add_migration($query) {
//     global $conn;
//         try {
//             $migration_query = $conn->prepare($query);
//             $migration_query->execute();
//             unset($conn);
//             return true;

//         } catch(PDOException $err) {
//             return false;

//         };
// }


// $add_is_ad_to_usersges = "ALTER TABLE `chusers` ADD `is_an_admin` TINYINT(1) NOT NULL DEFAULT '0' AFTER `username`; ";
// if (klxc_add_migration($add_is_ad_to_usersges)) {
//     echo 'added add_is_ad_to_usersges';
// } else {
//     echo 'error add_is_ad_to_usersges';
// };





?>