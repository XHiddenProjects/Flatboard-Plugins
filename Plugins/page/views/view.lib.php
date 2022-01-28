<?php
class getViews{
    public static function toCalc($views){
         $views = floatval($views);
    if($views < 1000){
         return $views;
    }
    if($views >= 1000 && $views < 1000000){
            return number_format($views/1000,1,'.','').'K';
    }
     if($views >= 1000000 && $views < 1000000000){
            return number_format($views/1000000,1,'.','').'M';
    }
     if($views >= 1000000000 && $views < 1000000000000){
            return number_format($views/1000000000,1,'.','').'B';
    }
    if($views >= 1000000000000){
                  return number_format($views/1000000000000,1,'.','').'T';
        
    }
    }
     public static function ConvertToBadge($getView){
    $badge = '<button type="button" class="btn btn-secondary" style="position:absolute;top:15%;right:20%;">
        <i class="fas fa-eye"></i>&nbsp;&nbsp;<span class="badge badge-info">'.$getView.'</span>
   </button>';
   echo $badge;
}
   
}

?>
