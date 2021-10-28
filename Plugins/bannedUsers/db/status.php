<?php 
if(isset($_GET['unban'])){
$plugin = "bannedUsers";
 $dom = $_SERVER['DOCUMENT_ROOT'] . "/forum/plugin/" . $plugin . "/". "db". "/" . $_GET['unban'];
                $createFile = fopen($dom, "w+");
                    fwrite($createFile, '{"username":"'.str_replace(".dat.json", "", $_GET['unban']).'", "isBanned":"", "bannedMessage": ""}');
                    fclose($createFile);
    echo "<script>window.history.back();</script>";


}else if(isset($_GET['ban'])){

$plugin = "bannedUsers";
 $dom = $_SERVER['DOCUMENT_ROOT'] . "/forum/plugin/" . $plugin . "/". "db". "/" . $_GET['ban'];
                $createFile = fopen($dom, "w+");
                $msg = htmlspecialchars('<h1 style="text-align:center;">You are banned</h1>');
                    fwrite($createFile, '{"username":"'.str_replace(".dat.json", "", $_GET['ban']).'", "isBanned":"on", "bannedMessage": "'.$msg.'"}');
                    fclose($createFile);
    echo "<script>window.history.back();</script>";



}else{
     echo "Error: no action is found";
}
?>