<?php

$conversation_id = $_GET['conversation_id'];



$uploads = get_uploads($conversation_id);



echo json_encode($uploads);



?>