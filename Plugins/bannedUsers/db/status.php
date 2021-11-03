<?php
$getRoot = str_replace("/plugin/bannedUsers/db","",dirname(__FILE__));

if(isset($_GET['unban'])){
$plugin = "bannedUsers";
 $dom = $getRoot . "/plugin/" . $plugin . "/". "db". "/" . $_GET['unban'];
                $createFile = fopen($dom, "w+");
                    fwrite($createFile, '{"username":"'.str_replace(".dat.json", "", $_GET['unban']).'", "isBanned":"", "bannedMessage": ""}');
                    fclose($createFile);
    echo "<script>window.history.back();</script>";


}else if(isset($_GET['ban'])){

$plugin = "bannedUsers";
 $dom = $getRoot . "/plugin/" . $plugin . "/". "db". "/" . $_GET['ban'];
                $createFile = fopen($dom, "w+");
                $msg = htmlspecialchars('<h1 style="text-align:center;">You are banned</h1>');
                    fwrite($createFile, '{"username":"'.str_replace(".dat.json", "", $_GET['ban']).'", "isBanned":"on", "bannedMessage": "'.$msg.'"}');
                    fclose($createFile);
    echo "<script>window.history.back();</script>";



}else if(isset($_GET['delete'])){
    $plugin = "bannedUsers";
 $dom = $getRoot . "/plugin/" . $plugin . "/". "db". "/" . $_GET['delete'];
 if(unlink($dom)){
    echo "<script>window.history.back();</script>";
 }else{
     echo "<script>window.history.back();</script>";
 }
}else if(isset($_GET['repend'])){
    $plugin = "bannedUsers";
 $dom = $getRoot . "/plugin/" . $plugin . "/". "db". "/" . $_GET['repend'];
$createFile = fopen($dom, "w+");
                    fwrite($createFile, '{"username":"'.$_GET['repend'].'", "isBanned":"", "bannedMessage": "", "ip": "", "status": "pending", "timeStamp":"'.date("Y-m-d").'"}');
                    fclose($createFile);
                echo "<script>window.history.back();</script>";    

}else{
     echo "Error: no action is found";
}
?>