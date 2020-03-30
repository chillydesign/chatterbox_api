<?php


$json = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);


if (!empty($data->attributes)) {


    $user_attributes = $data->attributes;

    $any_with_same_email = get_user_from_email($user_attributes->email);

    if (!$any_with_same_email) {

        $user_id = create_user($user_attributes);

        if ($user_id) {

            $user = get_user($user_id);

            http_response_code(201);
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode( 'Error');
        }

    } else {
        http_response_code(404);
        echo json_encode( 'email already taken');
    }




} else {
    http_response_code(404);
    echo json_encode( 'Error'  );
}
?>
