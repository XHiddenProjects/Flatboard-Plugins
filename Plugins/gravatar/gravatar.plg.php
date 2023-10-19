<?php defined('FLATBOARD') or die('Flatboard Community.');
/**
 * Core: include Infinite Ajax Scroll, font awesome 4.7, category icon picker and mini colors JS
 *
 * @author 		Frédéric K.
 * @copyright	(c) 2015-2019
 * @license		http://opensource.org/licenses/MIT
 * @package		FlatBoard
 * @version		2.1
 * @update		2019-03-25
 */	
function get_gravatar( $email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = array() ) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}

function gravatar_install()
{
	global $lang;
	$plugin = 'gravatar';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state'] = false;  // Ne pas désactiver !   
	$data['users'] = [];
    flatDB::saveEntry('plugin', $plugin, $data);
}
function gravatar_head(){
  $plugin = 'gravatar';
  # Lecture des données
  $data = flatDB::readEntry('plugin', $plugin);
  if ($data[$plugin.'state'])
  	return '	<style>.avatar-status {border-radius: 50%; width: 15px;height: 15px; margin-left: -15px; position: absolute; border: 2px solid #fff;}.avatar-status.green{background: rgba(51, 217, 178, 1);box-shadow: 0 0 0 0 rgba(51, 217, 178, 1);animation: pulse-green 2s infinite}@keyframes pulse-green{0%{transform: scale(0.95);box-shadow: 0 0 0 0 rgba(51, 217, 178, 0.7)}70%{transform: scale(1);box-shadow: 0 0 0 10px rgba(51, 217, 178, 0)}100%{transform: scale(0.95);box-shadow: 0 0 0 0 rgba(51, 217, 178, 0)}}.avatar-status.red{background: rgba(255, 82, 82, 1);box-shadow: 0 0 0 0 rgba(255, 82, 82, 1);animation: pulse-red 2s infinite}@keyframes pulse-red{0%{transform: scale(0.95);box-shadow: 0 0 0 0 rgba(255, 82, 82, 0.7)}70%{transform: scale(1);box-shadow: 0 0 0 10px rgba(255, 82, 82, 0)}100%{transform: scale(0.95);box-shadow: 0 0 0 0 rgba(255, 82, 82, 0)}}figure:hover img {box-shadow: #e3e3e3 0 0 0 0.5rem;}figure img {transition: box-shadow 300ms ease-out;}</style>'.PHP_EOL;
}
function gravatar_menu(){
	global $lang;
	$plugin = 'gravatar';
	$out='';
	$d = flatDB::readEntry('plugin', $plugin);
	if($d[$plugin.'state']){
	$out.= '<li class="nav-item">
              <a class="nav-link" href="./view.php/plugin/'.$plugin.'" style="color: white;"><img src="https://cdn.iconscout.com/icon/free/png-512/free-gravatar-3521471-2944915.png?f=webp&w=256" width="16" height="16"/> '.$lang[$plugin.'name'].'</a>
            </li>';
	}
	return $out;
}
function gravatar_view($username){
	global $lang, $sessionTrip;
	$plugin = 'gravatar';
	$out = '';
	$d = flatDB::readEntry('plugin', $plugin);
	if($d[$plugin.'state']){
		$out.='<form method="post">
		<div class="row">
			<div class="col">
				<label class="form-label">'.$lang['username'].'</label>
				<input class="form-control" type="text" name="username" value="'.HTMLForm::trip($sessionTrip, $username).'" readonly/>
			</div>
			<div class="col">
				<label class="form-label">'.$lang['email'].'</label>
				<input class="form-control" type="email" name="email" required/>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<label class="form-label">'.$lang['size'].'</label>
				<select class="form-control" name="size">
					<option value="200">200</option>
					<option value="400">400</option>
					<option value="600">600</option>
					<option value="800">800</option>
				</select>
			</div>
			<div class="col">
				<label class="form-label">'.$lang['default'].'</label>
				<select class="form-control" name="default">
					';
					foreach($lang['dicons'] as $d => $i){
						$out.='<option value="'.$d.'">'.$i.'</option>';
					}
					$out.='
				</select>
			</div>
			<div class="col">
				<label class="form-label">'.$lang['rating'].'</label>
				<select class="form-control" name="rating">
					<option value="x">x</option>
					<option value="r">r</option>
					<option value="pg">pg</option>
					<option value="g">g</option>
				</select>
			</div>
		</div>
		<button class="btn btn-success w-100 mt-2" name="saveGravatar">'.$lang['submit'].'</button>
		</form>';
	}
	return $out;
}
if(isset($_POST['saveGravatar'])){
	global $lang;
	$plugin = 'gravatar';
	$user = HTMLForm::clean($_POST['username']);
	$size = HTMLForm::clean($_POST['size']);
	$def = HTMLForm::clean($_POST['default']);
	$rate = HTMLForm::clean($_POST['rating']);
	$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
	$d = flatDB::readEntry('plugin', $plugin);
	$d['users'][$user] = get_gravatar($email, $size, $def, $rate);
	flatDB::saveEntry('plugin', $plugin, $d);
}
function gravatar_profile($username){
	global $sessionTrip, $cur;
	$plugin = 'gravatar';
	$idention = flatDB::readEntry('plugin', 'identicon');
	$d = flatDB::readEntry('plugin', $plugin);
	$out='';
	$identity = '';
	$user = strstr($username, '@', true);
	if($d[$plugin.'state']){
			if(pluginIsHere('identicon') && IDENTICON_ONLINE) {	
			$trip = HTMLForm::trip($sessionTrip, $username);
			if($username === $trip)
				$usersStat = '<span class="avatar-status green"></span>';
			else
				$usersStat = '<span class="avatar-status red"></span>';	

			$online = $cur!=='home' && IDENTICON_ONLINE ? $usersStat : '';
		} else $online = '';
		$out = '<img class="rounded-circle" src="'.$d['users'][$username].'" width="50" height="50" alt="' .$user. '">' .$online. PHP_EOL; 
	}
	return $out;
}
?>