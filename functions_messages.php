<?php


function get_messages($conversation_id){
    global $conn;

    if ($conversation_id !== null) {
        $query = "SELECT *  FROM messages
        WHERE conversation_id = :conversation_id 
        ORDER BY messages.created_at ASC";
    } else {
        $query = "SELECT *  FROM messages ORDER BY  messages.conversation_id ASC, messages.created_at DESC";
    }

    try {

        $messages_query = $conn->prepare($query);
        $messages_query->bindParam(':conversation_id', $conversation_id);
        $messages_query->setFetchMode(PDO::FETCH_OBJ);
        $messages_query->execute();
        $messages_count = $messages_query->rowCount();

        if ($messages_count > 0) {
            $messages =  $messages_query->fetchAll();
            $messages = processMessages($messages);
        } else {
            $messages =  [];
        }

        unset($conn);
        return $messages;

    } catch(PDOException $err) {
        return [];
    };
}



function get_message($message_id = null) {
    global $conn;
    if ( $message_id != null) {

        try {
            $query = "SELECT * FROM messages WHERE messages.id = :id LIMIT 1";
            $message_query = $conn->prepare($query);
            $message_query->bindParam(':id', $message_id);
            $message_query->setFetchMode(PDO::FETCH_OBJ);
            $message_query->execute();

            $message_count = $message_query->rowCount();

            if ($message_count == 1) {
                $message =  $message_query->fetch();
                $message =  processMessage($message);
            } else {
                $message = null;
            }
            unset($conn);
            return $message;
        } catch(PDOException $err) {
            return null;
        };
    } else { // if message id is not greated than 0
        return null;
    }
}



function create_message($message) {
    global $conn;
    global $current_user;
 
    if  ( $current_user &&  !empty($message->conversation_id)  && !empty($message->content)  ){

  
     

        try {
            $query = "INSERT INTO messages
             (conversation_id, content, user_id) VALUES 
             (:conversation_id, :content, :user_id)";
            $message_query = $conn->prepare($query);
            $message_query->bindParam(':conversation_id', $message->conversation_id);
            $message_query->bindParam(':user_id', $current_user->id);
            $message_query->bindParam(':content', $message->content);
          
            $message_query->execute();
            $message_id = $conn->lastInsertId();
            unset($conn);
            return ($message_id);

        } catch(PDOException $err) {

            return false;

        };

    } else { // message conversation_id was blank
        return false;
    }


}




function update_message($message_id, $message) {
    global $conn;
    if ( $message_id > 0 ){
        try {

      

            $updated_at =   updated_at_string();
            $query = "UPDATE messages SET 
            `content` = :content, 
     
            WHERE id = :id";
            $message_query = $conn->prepare($query);
            $message_query->bindParam(':content', $message->content);
     
            $message_query->bindParam(':id', $message_id);
            $message_query->execute();
            unset($conn);
            return true;

        } catch(PDOException $err) {
            return false;

        };

    } else { // message name was blank
        return false;
    }

}




function delete_message($message_id) {

    global $conn;
    if ($message_id > 0) {

        try {
            $query = "DELETE FROM messages  WHERE id = :id    ";
            $message_query = $conn->prepare($query);
            $message_query->bindParam(':id', $message_id);
            $message_query->setFetchMode(PDO::FETCH_OBJ);
            $message_query->execute();
            unset($conn);
            return true;

        } catch(PDOException $err) {
            return false;
        };
    } else {
        return false;
    }

}


function processMessage($message) {

    $message->user_id =  intval($message->user_id);
    $message->id =  intval($message->id);
  
    $message->conversation_id =  intval($message->conversation_id);
    return $message;
}


function processMessages($messages) {


    $users = get_users();

   

    
    foreach ($messages as $message) {
       processMessage($message);

       $message->user = get_user_from_collection( $message->user_id, $users);
    }

    return $messages;
}

?>