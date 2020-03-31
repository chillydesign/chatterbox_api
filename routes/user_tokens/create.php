<?php


$json = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);


if (!empty($data->attributes)) {


    $user_attributes = $data->attributes;



        $user = get_user_from_password($user_attributes);

        if ($user) {

            $token = make_token_from_user_id($user->id);

            $resp = new stdClass();
            $resp->jwt = $token;
            http_response_code(201);
            echo json_encode($resp);
        } else {
            http_response_code(404);
            echo json_encode( 'Error1');
        }



} else {
    http_response_code(404);
    echo json_encode( 'Error2'  );
}
?>
