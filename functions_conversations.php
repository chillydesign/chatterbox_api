<?php



function get_conversations($opts = null){
    global $conn;

    if($opts == null) {
        $opts =  array('limit' => 10, 'offset' => 0);
    };


    try {
        $query = "SELECT *  FROM conversations
        ORDER BY conversations.created_at DESC
        LIMIT :limit OFFSET :offset ";
        $conversations_query = $conn->prepare($query);
        $conversations_query->bindParam(':limit', intval($opts['limit']), PDO::PARAM_INT);
        $conversations_query->bindParam(':offset', intval($opts['offset']), PDO::PARAM_INT);
        $conversations_query->setFetchMode(PDO::FETCH_OBJ);
        $conversations_query->execute();
        $conversations_count = $conversations_query->rowCount();

    

        if ($conversations_count > 0) {
            $conversations =  $conversations_query->fetchAll();
        } else {
            $conversations =  [];
        }

        unset($conn);
        return $conversations;

    } catch(PDOException $err) {
      
        return [];
    };
}




function count_conversations(){
    global $conn;



    try {
        $query = "SELECT id FROM conversations WHERE deleted = 0";
        $conversations_query = $conn->prepare($query);
        $conversations_query->setFetchMode(PDO::FETCH_OBJ);
        $conversations_query->execute();
        $conversations_count = $conversations_query->rowCount();

        return   $conversations_count;


        unset($conn);

    } catch(PDOException $err) {
        return 0;
    };
}



function get_conversation($conversation_id = null) {

    global $conn;
    if ( $conversation_id != null) {


        try {
            $query = "SELECT * FROM conversations WHERE conversations.id = :id LIMIT 1";
            $conversation_query = $conn->prepare($query);
            $conversation_query->bindParam(':id', $conversation_id);
            $conversation_query->setFetchMode(PDO::FETCH_OBJ);
            $conversation_query->execute();

            $conversation_count = $conversation_query->rowCount();

            if ($conversation_count == 1) {
                $conversation =  $conversation_query->fetch();
            } else {
                $conversation =  null;
            }

            unset($conn);
            return $conversation;
        } catch(PDOException $err) {
            return null;
        };
    } else { // if conversation id is not greated than 0
        return null;
    }
}




function create_conversation($conversation) {
    global $conn;
    global $current_user;
    if ( $current_user &&   !empty($conversation->title )){

        try {
            $query = "INSERT INTO conversations (title, user_id) VALUES (:title, :user_id)";
            $conversation_query = $conn->prepare($query);
            $conversation_query->bindParam(':title', $conversation->title);
            $conversation_query->bindParam(':user_id', $current_user->id);
            $conversation_query->execute();
            $conversation_id = $conn->lastInsertId();
            unset($conn);

            return ($conversation_id);

        } catch(PDOException $err) {

            return false;

        };

    } else { // conversation name was blank
        return false;
    }


}





function update_conversation($conversation_id, $conversation) {
    global $conn;
    if ( $conversation_id > 0 ){
        try {

            $updated_at = updated_at_string();
            $query = "UPDATE conversations SET `title` = :title,  `deleted` = :deleted, `updated_at` = :updated_at WHERE id = :id";
            $conversation_query = $conn->prepare($query);
            $conversation_query->bindParam(':title', $conversation->title);
            $conversation_query->bindParam(':deleted', $conversation->deleted);
            $conversation_query->bindParam(':updated_at', $updated_at);

            $conversation_query->bindParam(':id', $conversation_id);
            $conversation_query->execute();
            unset($conn);

            return true;

        } catch(PDOException $err) {
            return false;

        };

    } else { // conversation name was blank
        return false;
    }

}

// change the updated_at date
function touch_conversation($conversation_id) {
    global $conn;
    if ( $conversation_id > 0 ){
        try {
            $updated_at = updated_at_string();
            $query = "UPDATE conversations SET `updated_at` = :updated_at WHERE id = :id";
            $conversation_query = $conn->prepare($query);
            $conversation_query->bindParam(':updated_at', $updated_at);
            $conversation_query->bindParam(':id', $conversation_id);
            $conversation_query->execute();
            unset($conn);
            return true;
        } catch(PDOException $err) {
            return false;
        };

    } else { // conversation name was blank
        return false;
    }
}



function delete_conversation($conversation_id) {

    global $conn;
    if ($conversation_id > 0) {

        try {
            $query = "DELETE FROM conversations  WHERE id = :id    ";
            $conversation_query = $conn->prepare($query);
            $conversation_query->bindParam(':id', $conversation_id);
            $conversation_query->setFetchMode(PDO::FETCH_OBJ);
            $conversation_query->execute();

            unset($conn);
            return true;


        } catch(PDOException $err) {
            return false;
        };
    } else {
        return false;
    }

}




?>