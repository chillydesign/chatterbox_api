<?php



$messages = get_messages(null);


foreach($messages as $message) {
$message->user_id = intval($message->user_id);
    $message->id = intval($message->id);
    $user = get_user($message->user_id);
    $message->user =  $user;

}
    

echo json_encode($messages);


?>