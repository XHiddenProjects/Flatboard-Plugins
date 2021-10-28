<?php
function CreateUser($user, $isBanned="", $bannedMessage="", $ip){
    $plugin = 'bannedUsers';
 $dom = str_replace("https://surveybuilder.epizy.com", $_SERVER['DOCUMENT_ROOT'],HTML_PLUGIN_DIR) . $plugin . DS. "db". DS . $user.".dat.json";
       if(!file_exists($dom)){
           $createFile = fopen($dom, "w+");
                    fwrite($createFile, '{"username":"'.$user.'", "isBanned":"'.$isBanned.'", "bannedMessage": "'.$bannedMessage.'", "ip": "'.$ip.'"}');
                    fclose($createFile);
       }
}
?>