<?php


$limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;



$total_count = count_users();

$users = get_users(  array('limit' => $limit, 'offset' => $offset));


foreach($users as $user) {
    $user->id = intval($user->id);
}
    

$ret = new stdClass();
  $ret->users = $users;
  $ret->total_count = $total_count;

echo json_encode($ret);


?>