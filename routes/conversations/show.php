<?php




$id = $_GET['id'];

$conversation = get_conversation($id);
if ($conversation) {
    $messages = get_messages($id);
    $user = get_user($conversation->user_id);
    $conversation->messages = $messages;


    $conversation->user_id = intval($conversation->user_id);
    $conversation->id = intval($conversation->id);
    $conversation->deleted =  intval($conversation->deleted);
    $conversation->user =  $user;

    echo json_encode($conversation);
    
} else {
    http_response_code(404);
    echo json_encode('error'); 
}







?>