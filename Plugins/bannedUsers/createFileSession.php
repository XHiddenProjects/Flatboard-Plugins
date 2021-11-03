<?php
function CreateUser($user, $isBanned="", $bannedMessage="", $ip, $status="active", $timeStamp){
    $plugin = 'bannedUsers';
 $dom = PLUGIN_DIR . $plugin . DS. "db". DS . $user.".dat.json";
       if(!file_exists($dom)){
           $createFile = fopen($dom, "w+");
                    fwrite($createFile, '{"username":"'.$user.'", "isBanned":"'.$isBanned.'", "bannedMessage": "'.$bannedMessage.'", "ip": "'.$ip.'", "status": "'.$status.'", "timeStamp": "'.$timeStamp.'"}');
                    fclose($createFile);
       }else{
           $check = file_get_contents($dom);
           $query = json_decode($check);
           if($query->status === "pending"){
               $createFile = fopen($dom, "w+");
                    fwrite($createFile, '{"username":"'.$user.'", "isBanned":"'.$isBanned.'", "bannedMessage": "'.$bannedMessage.'", "ip": "'.$ip.'", "status": "'.$status.'", "timeStamp": "'.$timeStamp.'"}');
                    fclose($createFile);
           }
       }
}
?>