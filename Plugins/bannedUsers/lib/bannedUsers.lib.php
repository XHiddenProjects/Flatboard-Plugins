<?php
function CheckBanned($user){
    $plugin = "bannedUsers";
$dom = PLUGIN_DIR . $plugin . DS. "db". DS . $user;
  $getContent = file_get_contents($dom);
$query = json_decode($getContent);
if($query->isBanned === "on"){
    return true;
}else{
    return false;
}
}


?>