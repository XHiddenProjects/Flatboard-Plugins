<?php
if(!isset($_GET['s']) || !isset($_GET['r']) || !isset($_GET['session'])){
    echo "You are not using a valid query selector";
}
$remote = $_GET['session'];
$dir = $_SERVER['DOCUMENT_ROOT'].str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__));
 $db_read = $dir."/db/userList/".$remote."/stared/";
if($_GET['s'] === "1"){
    if(unlink($db_read.$_GET['r'])){
       echo "<script>
       setTimeout(function(){
           history.back();
       });
       </script>";
    }else{
        echo "Error, cannot remove file";
    }
}
if($_GET['s'] === "0"){
if(fopen($db_read.$_GET['r'], "w+")){
echo "<script>
       setTimeout(function(){
           history.back();
       });
       </script>";
}else{
    echo "Error, cannot add file";
}
}
?>