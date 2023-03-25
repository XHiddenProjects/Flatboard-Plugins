<?php defined('FLATBOARD') or die('Flatboard Community.');

function birthday_install()
{
	global $lang;
	$plugin = 'birthday';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= false;
	$data['userList']   			= [];  
	flatDB::saveEntry('plugin', $plugin, $data);
}
function birthday_menu(){
	global $lang;
	$plugin='birthday';
	$out='';
	$d = flatDB::readEntry('plugin',$plugin);
	if($d[$plugin.'state']){
		$out.='<li class="nav-item">
              <a class="nav-link" href="view.php/plugin/'.$plugin.'" style="color: white;"><i class="fa fa-birthday-cake" aria-hidden="true"></i> '.$lang[$plugin.'name'].'</a>
            </li>';
	}
	return $out;
}
function birthday_view(){
	global $lang, $sessionTrip;
	$plugin='birthday';
	$out='';
	$d = flatDB::readEntry('plugin',$plugin);
	if($d[$plugin.'state']&&isset($sessionTrip)){
		$trip = preg_replace('/@[\w]+/','',$sessionTrip);
		$out.='<form method="post">
		<div class="row">
		<div class="col">
		<input type="text" name="username" class="form-control" readonly="" value="'.$trip.'"/>
		</div>
		<div class="col">
		<input type="date" name="userbday" class="form-control" value="'.(isset($d['userList'][$trip])&&array_key_exists($trip,$d['userList']) ? $d['userList'][$trip] : '').'"/>
		</div>
		</div>
		';
			if(isset($_POST['sbt'])){
	$username = $_POST['username'];
	$bday = (isset($_POST['userbday']) ? $_POST['userbday'] : '');
	if($bday!==''){
		$d['userList'][$username] = $bday;
		if(FlatDB::saveEntry('plugin', $plugin, $d))
			$out.= '<div class="alert alert-success m-2" role="alert">'.$lang['success_date'].'</div>';;
	}else{
		$out.= '<div class="alert alert-danger m-2" role="alert">'.$lang['invalid_date'].'</div>';
	}
}
		$out.='
		<input type="submit" name="sbt" class="btn btn-primary w-100 mt-2"/>
		</form>';
	}
	return $out;
}
function birthday_beforeMain(){
	global $lang, $sessionTrip;
	$plugin='birthday';
	$out='';
	$trip = preg_replace('/@[\w]+/','',$sessionTrip);
	$d = flatDB::readEntry('plugin',$plugin);
	$listUser=[];
	if($d[$plugin.'state']){
		foreach($d['userList'] as $username => $date){
			$md = preg_replace('/[\d]{4}-/','',$date);
			if(date('m-d')===$md){
				preg_match('/[\d]{4}/', $date, $y);
				$d1 = date_create($date);
				$d2 = date_create(date('Y-m-d'));
				$diff = date_diff($d1,$d2);
				$listUser[] = $username.'('.$diff->format("%Y").')';
			}
		}
	}
	$out .= (!empty($listUser) ? '<div class="alert alert-info h2 mt-3">'.$lang['intro_bday'].implode(',',$listUser).'!!!</div>' : '');
	return $out;
}
?>