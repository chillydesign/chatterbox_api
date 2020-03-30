<?php


$json = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);

if (!empty($data->attributes)) {


    $conversation_attributes = $data->attributes;
    $conversation_id = create_conversation($conversation_attributes);

    if ($conversation_id) {
        $conversation = get_conversation($conversation_id);
        http_response_code(201);
        echo json_encode($conversation);
    } else {
        http_response_code(404);
        echo json_encode( 'Error' );
    }



} else {
    http_response_code(404);
    echo json_encode( 'Error'  );
}




