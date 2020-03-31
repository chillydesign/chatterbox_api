<?php

$id = $_GET['id'];
$json = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);

if (!empty($data->attributes)) {


    if (isset($_GET['setapproval'])) {
        $updated = set_user_approval($id, $data->attributes->is_approved);
       
    } else {
        $user_attributes = $data->attributes;
        $updated = update_user($id, $user_attributes);
    }


    if ($updated) {
        $user = get_user($id);
        // if ($user) {
        
        // }
    
        http_response_code(200);
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode( 'Error'  );
    }



} else {
    http_response_code(404);
    echo json_encode( 'Error'  );
}


?>