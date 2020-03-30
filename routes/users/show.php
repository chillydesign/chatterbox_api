<?php

$id = $_GET['id'];

if ($id == 'me') {



    if ($current_user) {
        echo json_encode($current_user);        
    }
    

} else {
    $user = get_user($id);


    if ($user) {
        echo json_encode($user);        
    } else {
        http_response_code(404);
        echo json_encode('error'); 
    }
    
}








?>