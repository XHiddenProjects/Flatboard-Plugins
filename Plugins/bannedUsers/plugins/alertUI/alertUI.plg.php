<?php
class alertUI{
       
    public function replace($word, $replace, $str){
        return str_replace($word, $replace, $str);
    }
    public function create($id, $msg){  
          $getRoot = str_replace("/plugin/bannedUsers/plugins/alertUI","",dirname(__FILE__));
      $config = $getRoot . "/plugin/bannedUsers/plugins/alertUI/alertUI.conf.json";
       $config = file_get_contents($config);
        $query = json_decode($config);
        $type = $query->alert->type;
        $alert = '';
        $alert .= '<div class="alertUI" id="'.$id.'">';
        $alert .= '<div class="alert-'.$type.' '.$type.'" role="alert">'.$msg.'</h1>';
        $alert.='</div>';
        return $alert;
       
    }
    public function style(){
  $getRoot = str_replace("/plugin/bannedUsers/plugins/alertUI","",dirname(__FILE__));
      $config = $getRoot . "/plugin/bannedUsers/plugins/alertUI/alertUI.conf.json";
      $config = file_get_contents($config);
        $query = json_decode($config);
                $style = "<style>
        .alertUI{
            width:100%;
            height:15%;
            font-size:32px;
           
        }</style>
       ";
        
 return $style;
        }
       
        


        

    }

?>