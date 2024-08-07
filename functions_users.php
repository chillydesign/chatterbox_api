<?php



function get_users( $opts = null ){
    global $conn;

    if($opts == null) {
        $opts =  array('limit' => 2000, 'offset' => 0);
    };

    $query = "SELECT *  FROM chusers  ORDER BY chusers.created_at ASC  LIMIT :limit OFFSET :offset";
    $limit =  intval($opts['limit']);
    $offset = intval($opts['offset']);
    try {

        $users_query = $conn->prepare($query);
        $users_query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $users_query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $users_query->setFetchMode(PDO::FETCH_OBJ);
        $users_query->execute();
        $users_count = $users_query->rowCount();

        if ($users_count > 0) {
            $users =  $users_query->fetchAll();
            $users = processUsers($users);
        } else {
            $users =  [];
        }

        unset($conn);
        return $users;

    } catch(PDOException $err) {
        return [];
    };
}



function get_user_from_collection($user_id, $users) {


    foreach($users as $user) {

        if (  intval($user->id) == intval($user_id)) {
            return processUser($user);
        }
    }

    return null;

}

function get_user($user_id = null) {
    global $conn;
    if ( $user_id != null) {

        try {
            $query = "SELECT * FROM chusers WHERE chusers.id = :id LIMIT 1";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':id', $user_id);
            $user_query->setFetchMode(PDO::FETCH_OBJ);
            $user_query->execute();

            $user_count = $user_query->rowCount();

            if ($user_count == 1) {
                $user =  $user_query->fetch();
                $user =  processUser($user);
            } else {
                $user = null;
            }
            unset($conn);
            return $user;
        } catch(PDOException $err) {
            return null;
        };
    } else { // if user id is not greated than 0
        return null;
    }
}


function count_users(){
    global $conn;
    try {
        $query = "SELECT id FROM chusers";
        $users_query = $conn->prepare($query);
        $users_query->setFetchMode(PDO::FETCH_OBJ);
        $users_query->execute();
        $users_count = $users_query->rowCount();

        return $users_count;
        unset($conn);

    } catch(PDOException $err) {
        return 0;
    };
}



function encrypt_password($password) {

    $encrypted_password =  crypt( $password, USSLT  );
    return $encrypted_password;
}

function create_user($user) {
    global $conn;
    if ( !empty($user->email)   && !empty($user->username)  && !empty($user->password)  && !empty($user->password_confirmation)  ){
        if ($user->password == $user->password_confirmation) {

            $password_digest = encrypt_password($user->password);

            $email = strtolower($user->email);

                try {
                    $query = "INSERT INTO chusers
                     (email, password_digest, username) VALUES 
                     (:email, :password_digest, :username)";
                    $user_query = $conn->prepare($query);
                    $user_query->bindParam(':email',  $email);
                    $user_query->bindParam(':username', $user->username);
                    $user_query->bindParam(':password_digest',$password_digest);
                  
                    $user_query->execute();
                    $user_id = $conn->lastInsertId();
                    unset($conn);
                    return ($user_id);
        
                } catch(PDOException $err) {
        
                    return false;
        
                };
     
        
   
    } else {
 // confiramtion doesnt match
        return false;
    }

    } else { // user conversation_id was blank
        return false;
    }


}




function update_user($user_id, $user) {
    global $conn;
    if ( $user_id > 0 ){

        $email = strtolower($user->email);
        try {
            $updated_at =   updated_at_string();
            $query = "UPDATE chusers SET 
            `email` = :email, 
            `username` = :username, 
            WHERE id = :id";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':username', $user->username);
            $user_query->bindParam(':email', $email);
            $user_query->bindParam(':id', $user_id);
            $user_query->execute();
            unset($conn);
            return true;

        } catch(PDOException $err) {
            return false;

        };

    } else { // user name was blank
        return false;
    }

}




function set_user_approval($user_id, $approved) {
    global $conn;
    if ( $user_id > 0 ){


        if ($approved) {
            $approved_bool = 1;
        } else {
            $approved_bool = 0;
        }
        try {

            $query = "UPDATE chusers SET   `is_approved` = :is_approved  WHERE id = :id";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':is_approved', $approved_bool);
            $user_query->bindParam(':id', $user_id);
            $user_query->execute();
            unset($conn);
            return true;

        } catch(PDOException $err) {
            return false;
        };

    } else { // user name was blank
        return false;
    }

}

function make_token_from_user_id($user_id= null) {
    if ($user_id ){
  
        return intval($user_id) * JWTKEY;
    }
}
function make_user_id_from_token($token= null) {
    if ($token ){
        return  intval($token) /  JWTKEY;
    }
}


function get_user_from_password( $user = null) {

    global $conn;
    if ( $user != null ) {

    

        $email = strtolower($user->email);
        $password_digest = encrypt_password($user->password);

        try {
            $query = "SELECT * FROM chusers WHERE email = :email AND password_digest = :password_digest LIMIT 1";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':email', $email);
            $user_query->bindParam(':password_digest', $password_digest);
            $user_query->setFetchMode(PDO::FETCH_OBJ);
            $user_query->execute();

            $user_count = $user_query->rowCount();


            if ($user_count == 1) {
                $user =  $user_query->fetch();
                return  processUser($user);
            } else {
                return false;
            }

            unset($conn);
        } catch(PDOException $err) {
            return false;
        };
    } else { //  if no token sent
        return false;
    }
}


function get_user_from_email( $email=null) {

    global $conn;
    if ( $email != null ) {

        $email =  strtolower($email);

        try {
            $query = "SELECT * FROM chusers WHERE email = :email LIMIT 1";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':email',  $email);
            $user_query->setFetchMode(PDO::FETCH_OBJ);
            $user_query->execute();

            $user_count = $user_query->rowCount();


            if ($user_count == 1) {
                $user =  $user_query->fetch();
                return processUser($user);
            } else {
                return false;
            }

            unset($conn);
        } catch(PDOException $err) {
            return false;
        };
    } else { //  if no token sent
        return false;
    }
}




function get_user_from_token( $token=null) {

    global $conn;
    if ( $token != null ) {

        $id  = make_user_id_from_token($token);

        try {
            $query = "SELECT * FROM chusers WHERE id = :id LIMIT 1";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':id', $id);
            $user_query->setFetchMode(PDO::FETCH_OBJ);
            $user_query->execute();

            $user_count = $user_query->rowCount();


            if ($user_count == 1) {
                $user =  $user_query->fetch();
                return  processUser($user);
            } else {
                return false;
            }

            unset($conn);
        } catch(PDOException $err) {
            return false;
        };
    } else { //  if no token sent
        return false;
    }
}


function delete_user($user_id) {

    global $conn;
    if ($user_id > 0) {

        try {
            $query = "DELETE FROM chusers  WHERE id = :id    ";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':id', $user_id);
            $user_query->setFetchMode(PDO::FETCH_OBJ);
            $user_query->execute();
            unset($conn);
            return true;

        } catch(PDOException $err) {
            return false;
        };
    } else {
        return false;
    }

}


function processUser($user) {

 
    $user->id =  intval($user->id);
    $user->is_an_admin = ($user->is_an_admin == 1);
    $user->is_approved = ($user->is_approved == 1);
    unset($user->password_digest);
    return $user;
}


function processUsers($users) {
    
    foreach ($users as $user) {
       processUser($user);
    }

    return $users;
}
