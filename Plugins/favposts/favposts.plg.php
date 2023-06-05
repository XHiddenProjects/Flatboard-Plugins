<?php defined('FLATBOARD') or die('Flatboard Community.');

function favposts_install()
{
	global $lang;
	$plugin = 'favposts';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= false;
	 
	flatDB::saveEntry('plugin', $plugin, $data);
}
function favposts_view(){
	global $lang, $sessionTrip;
	$plugin = 'favposts';
	$d = FlatDB::readEntry('plugin',$plugin);
	$out='';
	if($d[$plugin.'state']&&isset($sessionTrip)&&preg_match('/[\w\-\_]/',$sessionTrip)){
		$out.='<form method="post">
		<div class="row">
			<div class="col">
			<label for="forum">'.$lang[$plugin.'forum'] .'</label>
			<select name="favposts" class="form-control">';
			foreach(FlatDB::listEntry('topic') as $topic){
					$t = flatDB::readEntry('topic', $topic);
					$out.='<option value="'.$topic.'">'.$t['title'].'</option>';
			} 
			$out.='</select>
				</div>
				<div class="col">
					<label for="forum">'.$lang[$plugin.'user'] .'</label>
					<input name="author" class="form-control" readonly value="'.$sessionTrip.'"/>
				</div>
			</div>
			<button class="btn btn-success w-100 mt-2" name="favsave"><i class="fa fa-save"></i></button>
		</form>';
		$out.='<form method="post" class="mt-3"><div class="row">
		<div class="col">
		<label for="forum">'.$lang[$plugin.'forum'] .'</label>
		<select class="form-control" name="favposts">';
		foreach($d[preg_replace('/\@[\w\-\_]+/','',$sessionTrip)] as $fav){
			$t = flatDB::readEntry('topic', $fav);
			$out.='<option value="'.$fav.'">'.$t['title'].'</option>';
		}
		$out.='</select></div>
		<div class="col">
					<label for="forum">'.$lang[$plugin.'user'] .'</label>
					<input name="author" class="form-control" readonly value="'.$sessionTrip.'"/>
				</div></div>
		<button class="btn btn-danger w-100 mt-2" name="favtrash"><i class="fa fa-trash"></i></button>
		</form>';
	}
	
	if(isset($_POST['favsave'])){
		$name = preg_replace('/\@[\w\-\_]+/','',str_replace('\s','',$_POST['author']));
		$topic = $_POST['favposts'];
			$d[$name][$topic] = $topic;
		if(@FlatDB::saveEntry('plugin',$plugin,$d)){
			$out.='<script plugin-script-name="'.$plugin.'">window.open("view.php/plugin/'.$plugin.'", "_self");</script>';
		}
	}
	if(isset($_POST['favtrash'])){
		$name = preg_replace('/\@[\w\-\_]+/','',str_replace('\s','',$_POST['author']));
		$topic = $_POST['favposts'];
		unset($d[$name][$topic]);
		if(@FlatDB::saveEntry('plugin',$plugin,$d)){
			$out.='<script plugin-script-name="'.$plugin.'">window.open("view.php/plugin/'.$plugin.'", "_self");</script>';
		}
	}
	return $out;
}
function favposts_menu(){
	global $lang;
	$plugin='favposts';
	$out='';
	$d = flatDB::readEntry('plugin',$plugin);
	if($d[$plugin.'state']){
		$out.='<li class="nav-item">
              <a class="nav-link" href="view.php/plugin/'.$plugin.'" style="color: white;"><i class="fa fa-star" aria-hidden="true"></i> '.$lang[$plugin.'name'].'</a>
            </li>';
	}
	return $out;
}
function favposts_init(){
	global $lang, $sessionTrip;
	$plugin = 'favposts';
	$d = FlatDB::readEntry('plugin',$plugin);
	$out='';
	if($d[$plugin.'state']){
		if(isset($sessionTrip)&&preg_match('/[\w\-\_]/',$sessionTrip)){
		$sessionTrip = preg_replace('/\@[\w\-\_]+/','',$sessionTrip);
		foreach(FlatDB::listEntry('topic') as $topic){
			if(isset($d[$sessionTrip][$topic])){
				$t = flatDB::readEntry('topic', $topic);
				$t['title'] = (preg_match('/\<span class=\"badge bg-warning\"\>\<i data-toggle=\"tooltip\" data-placement=\"top\" title=\"'.$lang[$plugin.'favlabel'].'\" class=\"fa fa-star\"\>\<\/i\>\<\/span\>/', $t['title']) ? str_replace(' <span class="badge bg-warning"><i data-toggle="tooltip" data-placement="top" title="'.$lang[$plugin.'favlabel'].'" class="fa fa-star"></i></span>', ' <span class="badge bg-warning"><i data-toggle="tooltip" data-placement="top" title="'.$lang[$plugin.'favlabel'].'" class="fa fa-star"></i></span>', $t['title']) : $t['title'].' <span class="badge bg-warning"><i data-toggle="tooltip" data-placement="top" title="'.$lang[$plugin.'favlabel'].'" class="fa fa-star"></i></span>');
				FlatDB::saveEntry('topic', $topic, $t);
			}else{
				$t = flatDB::readEntry('topic', $topic);
				$t['title'] = str_replace(' <span class="badge bg-warning"><i data-toggle="tooltip" data-placement="top" title="'.$lang[$plugin.'favlabel'].'" class="fa fa-star"></i></span>','',$t['title']);
				FlatDB::saveEntry('topic', $topic, $t);
			}
			
			}
		}
	}else{
		foreach(FlatDB::listEntry('topic') as $topic){
			$t = flatDB::readEntry('topic', $topic);
			$t['title'] = str_replace(' <span class="badge bg-warning"><i data-toggle="tooltip" data-placement="top" title="'.$lang[$plugin.'favlabel'].'" class="fa fa-star"></i></span>','',$t['title']);
			FlatDB::saveEntry('topic', $topic, $t);
		}
	}
}
?>