<?php

$id = $_GET['id'];
$json = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);

if (!empty($data->attributes)) {


    $conversation_attributes = $data->attributes;
    $updated = update_conversation($id, $conversation_attributes);

    if ($updated) {
        $conversation = get_conversation($id);
        http_response_code(200);
        echo json_encode($conversation);
    } else {
        http_response_code(404);
        echo json_encode( 'Error'  );
    }



} else {
    http_response_code(404);
    echo json_encode( 'Error'  );
}


?>