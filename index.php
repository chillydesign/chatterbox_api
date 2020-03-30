<?php

ini_set('default_charset', 'UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header('Content-Type: application/json;charset=UTF-8');



include('connect.php');
include('functions.php');


$all_headers = (getallheaders()) ;
$current_user = false;
if ( isset($all_headers['Authorization'])) {
   $bearer = $all_headers['Authorization'];
   $token = explode( 'Bearer ', $bearer);
   $current_user = get_user_from_token($token[1]);

}


if ( isset($_GET['route'])  ) {
    $route = $_GET['route'];


    if ($current_user) {

    if ($route == 'conversations') {
        if (isset($_GET['id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                include('routes/conversations/delete.php');
            } else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                 include('routes/conversations/update.php');
            } else {
                include('routes/conversations/show.php');
            }
        } else  {

    
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include('routes/conversations/create.php');
            } else {
                include('routes/conversations/index.php');
            }
        }
        } // end of if route is conversations

        if ($route == 'messages') {
            if (isset($_GET['id'])) {
                if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    include('routes/messages/delete.php');
                } else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                     include('routes/messages/update.php');
                } else {
                    include('routes/messages/show.php');
                }
            } else  {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    include('routes/messages/create.php');
                } else {
                    include('routes/messages/index.php');
                }
            }
            
        } // end of if route is messages
    
    }


  
    if ($route == 'user_tokens') {
        
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include('routes/user_tokens/create.php');
            } 
        
    } // end of if route is users



    if ($route == 'users') {
        if (isset($_GET['id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                include('routes/users/delete.php');
            } else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                 include('routes/users/update.php');
            } else {
                include('routes/users/show.php');
            }
        } else  {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include('routes/users/create.php');
            } else {
                include('routes/users/index.php');
            }
        }
        
    } // end of if route is users


    if ($route == 'uploads') {
        if (isset($_GET['id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                include('routes/uploads/delete.php');
            } else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                 include('routes/uploads/update.php');
            } else {
                include('routes/uploads/show.php');
            }
        } else  {

   
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include('routes/uploads/create.php');
            } else {
                include('routes/uploads/index.php');
            }
        }
    } // end of if route is messages




} else {
   //  error
   http_response_code(404);
   echo json_encode('error'); 

}





?>
