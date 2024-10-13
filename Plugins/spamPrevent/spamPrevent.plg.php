<?php defined("FLATBOARD") or die("Flatboard community");
function spamPrevent_install(){
    global $lang;
	$plugin = 'spamPrevent';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= false; 
    $data[$plugin.'Measure']        = 50;
    flatDB::saveEntry('plugin', $plugin, $data);
}
function spamPrevent_config(){
    global $lang, $token;
	$plugin = 'spamPrevent';
    $out = '';
    if(User::isAdmin()){
        if(!empty($_POST) && CSRF::check($token)){
            $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : ''; 
            $data[$plugin.'Measure'] 		 = HTMLForm::clean($_POST['spam_measure']); 
            flatDB::saveEntry('plugin', $plugin, $data);
            $out .= Plugin::redirectMsg($lang['data_save'],'config.php' . DS . 'plugin' . DS . $plugin, $lang['plugin'].'&nbsp;<b>' .$lang[$plugin.'name']. '</b>');
        }else{
            if (flatDB::isValidEntry('plugin', $plugin)) $data = flatDB::readEntry('plugin', $plugin);
            $out .= HTMLForm::form('config.php' . DS . 'plugin' . DS . $plugin, '
            <div class="row">
            <div class="col">
            '.HTMLForm::checkBox('state', $data[$plugin.'state']).'
            </div>
            </div>
            <div class="row">
                <div class="col">
                    <label class="form-label">'.$lang[$plugin.'Measure'].'</label>
                    <div class="input-group">
                        <input type="number" name="spam_measure" class="form-control" min="0" max="100" value="'.(isset($data[$plugin.'Measure']) ? $data[$plugin.'Measure'] : 66).'"/>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div><br/>'.HTMLForm::simple_submit());
        }
        return $out;
    }
}

function delTopicFromForum($id){
    foreach(array_diff(scandir(DATA_DIR.'forum'),['.','..']) as $forum){
        $forum = preg_replace('/\.dat\.php/','',$forum);
        $forumID = $forum;
        $forum = flatDB::readEntry('forum',$forum);
        unset($forum['topic'][$id]);
        flatDB::saveEntry('forum',$forumID,$forum);
    }
}

function spamPrevent_init(){
    $plugin = 'spamPrevent';
    $checkAI = array();
    if(flatDB::isValidEntry('plugin',$plugin)) $data = flatDB::readEntry('plugin',$plugin) ;
    
    if($data[$plugin.'state']){
        foreach(array_diff(scandir(DATA_DIR.'topic'),['.','..']) as $topic){
            $topic = preg_replace('/\.dat\.php/','',$topic);
            $id = $topic;
            $topic = flatDB::readEntry('topic',$topic);

            if(empty($checkAI)){
                array_push($checkAI,['title'=>$topic['title'], 'id'=>$id]);
            }else{
                for($i=0;$i<count($checkAI);$i++){
                    if(isset($checkAI[$i]['title']) && isset($topic['title'])){
                        similar_text($checkAI[$i]['title'],$topic['title'],$percent);
                        if($percent>=floatval($data[$plugin.'Measure'])){
                            if(file_exists(DATA_DIR.'topic/'.$id.'.dat.php')){
                                delTopicFromForum($id);
                                unlink(DATA_DIR.'topic/'.$id.'.dat.php');
                            }
                            else
                                die($plugin.': Error No File->'.(DATA_DIR.'topic/'.$id.'.dat.php'));
                        }
                    }
                }
                array_push($checkAI,['title'=>$topic['title'],'id'=>$id]);
            }
        }
    }
}
?>