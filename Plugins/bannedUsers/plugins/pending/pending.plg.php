<?php
class Pending{
   public function setDay($day){
      $getRoot = str_replace("/plugin/bannedUsers/plugins/pending","",dirname(__FILE__));
      $config = $getRoot . "/plugin/bannedUsers/plugins/pending/pending.conf.json";
      fopen($config, "w+");
      $write = '{
          "timeStamp": '.$day.'
      }';
      fwrite($config, $write);
      fclose($config);
   }
    public function getDay(){
      $getRoot = str_replace("/plugin/bannedUsers/plugins/pending","",dirname(__FILE__));
    $config = $getRoot . "/plugin/bannedUsers/plugins/pending/pending.conf.json";
      $content = file_get_contents($config);
      $query = json_decode($content);
      return $query->timeStamp;
    }
}

?>
