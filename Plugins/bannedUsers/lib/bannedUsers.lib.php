<?php
class bannedUsers{
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
function isPending($user){
      $plugin = "bannedUsers";
$dom = PLUGIN_DIR . $plugin . DS. "db". DS . $user;
  $getContent = file_get_contents($dom);
$query = json_decode($getContent);
if($query->status === "pending"){
    return true;
}else{
    return false;
}
}

function checkPending($user){
      $plugin = "bannedUsers";
$dom = PLUGIN_DIR . $plugin . DS. "db". DS . $user;
  $getContent = file_get_contents($dom);
$query = json_decode($getContent);
if($query->status === "pending"){
   $getFuture = date("Y-m-d", strtotime($query->timeStamp. " + 5 days"));
    $set = date("Y-m-d");
   if($set < $getFuture){
       return true;
   }else{
       return false;
   }
}else{
    return false;
}
}

function pendingExpire($user){
          $plugin = "bannedUsers";
$dom = PLUGIN_DIR . $plugin . DS. "db". DS . $user;
  $getContent = file_get_contents($dom);
$query = json_decode($getContent);
if($query->status === "pending"){
       $getFuture = date("Y-m-d", strtotime($query->timeStamp. " + 5 days"));
    $set = date("Y-m-d");
   if($set <= $getFuture){
       $start = date_create($set);
       $end = date_create($getFuture);
       $cal=date_diff($start,$end);
       $format = $cal->format("%d Days");
       return $format;
   }else{
     return true;
   }
}else{
    return false;
}
}

function isFailed($user){
          $plugin = "bannedUsers";
$dom = PLUGIN_DIR . $plugin . DS. "db". DS . $user;
  $getContent = file_get_contents($dom);
$query = json_decode($getContent);
if($query->status === "failed"){
    return true;
}else{
    return false;
}
}
function isActive($user){
          $plugin = "bannedUsers";
$dom = PLUGIN_DIR . $plugin . DS. "db". DS . $user;
  $getContent = file_get_contents($dom);
$query = json_decode($getContent);
if($query->status === "active"){
    return true;
}else{
    return false;
}
}
}

?>
