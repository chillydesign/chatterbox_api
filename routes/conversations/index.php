<?php


$limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;

$total_count = count_conversations();

$conversations = get_conversations( array('limit' => $limit, 'offset' => $offset)  );
$users = get_users();


foreach($conversations as $conversation) {
    $conversation->user_id = intval($conversation->user_id);
    $conversation->id = intval($conversation->id);
 
  $conversation->user =  get_user_from_collection($conversation->user_id, $users);

}
    

$ret = new stdClass();
$ret->conversations = $conversations;
$ret->total_count = $total_count;

echo json_encode($ret);

// CANT GET ANGULAR TO WORK THIS
// header('X-Total-Count: '. $total_count);

?>