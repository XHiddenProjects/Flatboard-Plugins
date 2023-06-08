<?php defined('FLATBOARD') or die('Flatboard Community.');

function mindreader_install()
{
	global $lang;
	$plugin = 'mindreader';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= false;
	flatDB::saveEntry('plugin', $plugin, $data);
	$train = fopen(PLUGIN_DIR.$plugin.DS.'training.json','w+');
	fwrite($train,'');
	fclose($train);
}
function mindreader_init(){
	global $lang;
	$plugin = 'mindreader';
	$out='';
	$d = flatDB::readEntry('plugin', $plugin);
	$f = PLUGIN_DIR.$plugin.DS.'training.json';
	$t = json_decode(file_get_contents($f), true);
	if($d[$plugin.'state']){
			$t['summary'] = (isset($t['summary']) ? $t['summary'] : array());
			$t['replies'] = (isset($t['replies']) ? $t['replies'] : array());
	$e = json_encode($t);
	$o = fopen($f,'w+');
	fwrite($o,$e);
	fclose($o);
	}
}
function mindreader_menu(){
	global $lang, $sessionTrip;
	$plugin='mindreader';
	$out='';
	$d = flatDB::readEntry('plugin',$plugin);
	if($d[$plugin.'state']){
		$out.='<li class="nav-item">
              <a class="nav-link" href="view.php/plugin/'.$plugin.'" style="color: white;"><i class="fa fa-book" aria-hidden="true"></i> '.$lang[$plugin.'name'].'</a>
            </li>';
	}
	return $out;
}
function mindreader_view(){
	global $lang;
	$plugin = 'mindreader';
	$out='';
	$d = flatDB::readEntry('plugin', $plugin);
	$tr = json_decode(file_get_contents(PLUGIN_DIR.$plugin.DS.'training.json'), true);
	$tL='';
	$i=0;
	if($d[$plugin.'state']){
		$out.='<form method="post">
			<div class="row">
			<div class="col">
			<label for="summary" class="form-label">'.$lang[$plugin.'sum'].'</label>
			<textarea name="summary" id="botSum" class="form-control" style="height: 120px;">'.implode('
',$tr['summary']).'</textarea>
			</div>
			<div class="col">
			<label for="replies" class="form-label">'.$lang[$plugin.'rep'].'</label>
			<textarea name="replies" id="botRep" class="form-control" style="height: 120px;">'.implode('
',$tr['replies']).'</textarea>
			</div>
			</div>
			<div class="row">
				<div class="col">
					<button name="botSave" class="btn btn-success w-100 mt-2"><i class="fa fa-save"></i></button>
				</div>
			</div>
		</form>';
	}
	if(isset($_POST['botSave'])){
		$sum = ($_POST['summary']!=='' ? explode('
',HTMLForm::clean(preg_replace('/\r/','',$_POST['summary']))) : array());
		$rep = ($_POST['replies']!=='' ? explode('
',HTMLForm::clean(preg_replace('/\r/','',$_POST['replies']))) : array());
		$f = PLUGIN_DIR.$plugin.DS.'training.json';
		$t = json_decode(file_get_contents($f), true);
		$t['summary'] = ($sum!=='' ? $sum : array()); 
		$t['replies'] = ($rep!=='' ? $rep : array());
		$e = json_encode($t);
		$o = fopen($f,'w+');
		fwrite($o,$e);
		fclose($o);
		echo '<script plugin-script-name="'.$plugin.'">window.open("view.php/plugin/'.$plugin.'", "_self")</script>';
	}
	return $out;
}
function mindreader_buttonTopic(){
		global $lang;
	$plugin = 'mindreader';
	$out='';
	$d = flatDB::readEntry('plugin', $plugin);
	$f = PLUGIN_DIR.$plugin.DS.'training.json';
	$t = json_decode(file_get_contents($f), true);
	if($d[$plugin.'state']){
		$out.= '<form method="post"><button class="btn btn-primary" name="mrsummary" value="'.$_GET['topic'].'"><i class="fa fa-save"></i></button></form>';
	}
	if(isset($_POST['mrsummary'])){
		$tp = flatDB::readEntry('topic', $_POST['mrsummary']);
		!in_array($tp['content'], $t['summary']) ? array_push($t['summary'], $tp['content']) : '';
		$e = json_encode($t);
		$o = fopen($f,'w+');
		fwrite($o,$e);
		fclose($o);
	}
	return $out;
}
function mindreader_buttonReply(){
		global $lang, $reply;
	$plugin = 'mindreader';
	$out='';
	$d = flatDB::readEntry('plugin', $plugin);
	$f = PLUGIN_DIR.$plugin.DS.'training.json';
	$t = json_decode(file_get_contents($f), true);
	if($d[$plugin.'state']){
		$out.= '<form method="post"><button class="btn btn-primary" name="mrreply" value="'.$reply.'"><i class="fa fa-save"></i></button></form>';
	}
	if(isset($_POST['mrreply'])){
		$r = flatDB::readEntry('reply', $_POST['mrreply']);
		!in_array($r['content'], $t['replies']) ? array_push($t['replies'], $r['content']) : '';
		$e = json_encode($t);
		$o = fopen($f,'w+');
		fwrite($o,$e);
		fclose($o);
	}
	return $out;
}
function mindreader_editor(){
		global $lang;
	$plugin = 'mindreader';
	$out='';
	$d = flatDB::readEntry('plugin', $plugin);
	$f = PLUGIN_DIR.$plugin.DS.'training.json';
	$t = json_decode(file_get_contents($f), true);
	if($d[$plugin.'state']){

			$out.='<div class="box bg-dark text-light d-inline-block p-2 h4 rounded w-100 input-group m-1">
			<div class="custom-control custom-switch d-flex flex-row-reverse">
 <input type="checkbox" class="custom-control-input" id="mrSwitch">
  <label class="custom-control-label" for="mrSwitch">'.$lang[$plugin.'rep'].'</label>
</div>
			<div class="container-fluid p-4 mt-2 mb-2 rounded bg-info mrtxt"><span class="msg">'.
			$t['summary'][0].'</span>
			<button type="button" onclick="useRecommend(this)" class="btn btn-secondary float-right"><i class="fa fa-share-square-o" aria-hidden="true"></i></button>
			</div>
			<center><span style="cursor:pointer;" onclick="MRPageBack(\'Left\')"><i class="fa fa-arrow-left"></i></span> <span class="mrnow">1</span>/<span class="mrtotal">'.count($t['summary']).'</span> <span style="cursor:pointer;" onclick="MRPageBack(\'Right\')"><i class="fa fa-arrow-right"></i></span></center>
			</div>';
		
	}
	return $out;
}
function mindreader_footerJS(){
	global $lang;
	$plugin = 'mindreader';
	$out='';
	$d = flatDB::readEntry('plugin', $plugin);
	if($d[$plugin.'state']){
		$out.='<script plugin-script-name="'.$plugin.'">
		var mindreader = {path: "'.HTML_PLUGIN_DIR.$plugin.DS.'training.json"};</script>';
		$out.='<script plugin-script-name="'.$plugin.'" src="'.HTML_PLUGIN_DIR.$plugin.DS.'js'.DS.$plugin.'.min.js?v='.$lang[$plugin.'version'].'"></script>';
	}
	return $out;
}
?>