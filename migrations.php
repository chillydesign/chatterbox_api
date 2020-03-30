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


// $add_completed_at_to_messages = "ALTER TABLE `messages` ADD `completed_at` DATETIME DEFAULT NULL; ";
// if (klxc_add_migration($add_completed_at_to_messages)) {
//     echo 'added add_completed_at_to_messages';
// } else {
//     echo 'error add_completed_at_to_messages';
// };


// $add_priority_to_messages = "ALTER TABLE `messages` ADD `priority` TINYINT(1) NOT NULL DEFAULT '0' AFTER `indentation`; ";
// if (klxc_add_migration($add_priority_to_messages)) {
//     echo 'added add_priority_to_messages';
// } else {
//     echo 'error add_priority_to_messages';
// };


// $add_uploads_table = "CREATE TABLE `chatterbox_api`.`uploads` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `filename` TEXT NOT NULL , `extension` VARCHAR(255) NOT NULL , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `conversation_id` INT(11) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
// if (klxc_add_migration($add_uploads_table)) {
//     echo 'added add_uploads_table';
// } else {
//     echo 'error add_uploads_table';
// };



?>