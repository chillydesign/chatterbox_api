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



function add_file_to_message($message, $file) {
    global $conn;
    global $current_user;



    $filename = null;
    if ( $current_user &&  !empty($message->id) && !empty($file)) {
       try {

    

        $message_id = $message->id;

        $file_contents = $file;
        $filedata = explode(',', $file_contents);
        $decoded_file = base64_decode($filedata[1]); // remove the mimetype from the base 64 string


        $mime_type = finfo_buffer(finfo_open(), $decoded_file, FILEINFO_MIME_TYPE); // extract mime type
        $extension = mime2ext($mime_type); // extract extension from mime type


        $target_dir = FILELOC . UPLOADDIR; // add the specific path to save the file
        mkdir($target_dir . '/' . $message_id , 0777);

        $filename = 'file.' . $extension; 

      
        $file_dir = $target_dir . $message_id . '/' .  $filename ;
        file_put_contents($file_dir, $decoded_file); // save

        $query = "UPDATE messages SET  `file` = :file  WHERE id = :id";
        $message_query = $conn->prepare($query);
        $message_query->bindParam(':file', $filename);
        $message_query->bindParam(':id', $message_id);
        $message_query->execute();
        unset($conn);

        $message->file = $filename;
        $message->file_url =     UPLOADDIR . $message->id . '/'. $message->file;


        return true;
      } catch(PDOException $err) {
            return false;
        };
       
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



function update_message_conversation_count($message ) {

    global $conn;

    if ($message->conversation_id) {

        $message_count = count_messages_by_conversations_id($message->conversation_id);

        try {

            $query = "UPDATE conversations SET  `messages_count` = :messages_count WHERE id = :id";
            $conversation_query = $conn->prepare($query);
            $conversation_query->bindParam(':messages_count', $message_count);
            $conversation_query->bindParam(':id', $message->conversation_id);
            $conversation_query->execute();
            unset($conn);
            return true;

        } catch(PDOException $err) {
            return false;

        };

    };

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
    if ($message->file) {
        $message->file_url =     UPLOADDIR . $message->id . '/'. $message->file;
    }
  
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




/*
to take mime type as a parameter and return the equivalent extension
*/
function mime2ext($mime){
    $all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp",
    "image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp",
    "image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp",
    "application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg",
    "image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],
    "wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],
    "ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg",
    "video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],
    "kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],
    "rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application",
    "application\/x-jar"],"zip":["application\/x-zip","application\/zip",
    "application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],
    "7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],
    "svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],
    "mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],
    "webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],
    "pdf":["application\/pdf","application\/octet-stream"],
    "pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],
    "ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office",
    "application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],
    "xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],
    "xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel",
    "application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],
    "xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo",
    "video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],
    "log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],
    "wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],
    "tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop",
    "image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],
    "mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar",
    "application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40",
    "application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],
    "cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary",
    "application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],
    "ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],
    "wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],
    "dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php",
    "application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],
    "swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],
    "mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],
    "rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],
    "jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],
    "eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],
    "p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],
    "p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
    $all_mimes = json_decode($all_mimes,true);
    foreach ($all_mimes as $key => $value) {
        if(array_search($mime,$value) !== false) return $key;
    }
    return false;
}


?>