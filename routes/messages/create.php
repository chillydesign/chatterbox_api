<?php


$json = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);

if (!empty($data->attributes) && $current_user) {


    $message_attributes = $data->attributes;
    $message_id = create_message($message_attributes);

    if ($message_id) {
        
    
        $message = get_message($message_id);

        if ($message) {
            update_message_conversation_count($message);
            touch_conversation($message->conversation_id);
        }
  
        $message->user = $current_user;
       
        http_response_code(201);
        echo json_encode($message);
    } else {
        http_response_code(404);
        echo json_encode( 'Error' );
    }



} else {
    http_response_code(404);
    echo json_encode( 'Error'  );
}
?>
