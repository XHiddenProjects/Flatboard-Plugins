<?php
if(!isset($_GET['s']) && !isset($_GET['r']) && !isset($_GET['session']) || !isset($_GET['h']) && !isset($_GET['r']) && !isset($_GET['session'])){
    echo "You are not using a valid query url";
}
$remote = $_GET['session'];
$dir = $_SERVER['DOCUMENT_ROOT'].str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__));
 $db_read = $dir."/db/userList/".$remote."/readList/";
 $db_hide = $dir."/db/userList/".$remote."/hidden/";
 if(isset($_GET['url'])){
if($_GET['s'] === "1"){

       echo "<script>
       setTimeout(function(){
          window.open('".$_GET['url']."', '_self');
       });
       </script>";
   
}
if($_GET['s'] === "0"){
if(fopen($db_read.$_GET['r'], "x+")){
echo "<script>
       setTimeout(function(){
           window.open('".$_GET['url']."', '_self');
       });
       </script>";
}else{
    echo "Error, cannot add file";
}
}

}else{
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
if(fopen($db_read.$_GET['r'], "x+")){
echo "<script>
       setTimeout(function(){
           history.back();
       });
       </script>";
}else{
    echo "Error, cannot add file";
}
}
}

if($_GET['h'] === "true"){
  if(fopen($db_hide.$_GET['r'], "x+")){
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