<?php defined('FLATBOARD') or die('Flatboard Community.');

function favposts_install()
{
	global $lang;
	$plugin = 'favposts';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= false;
	foreach(FlatDB::listEntry('topic') as $topic){
		$data[$plugin.'postsLists'][$topic] = (int)0;
	}  
	flatDB::saveEntry('plugin', $plugin, $data);
}
function favposts_init(){
	global $lang;
	$plugin = 'favposts';
	$d = FlatDB::readEntry('plugin',$plugin);
	if($d[$plugin.'state']){
		/*setcookie*/
		arsort($d[$plugin.'postsLists']);
		$i=0;
		foreach($d[$plugin.'postsLists'] as $postList=>$view){
			if($i<1&&(int)$view>0){
				setcookie('favposts', $postList, time() + (86400 * 1826.25), '/');
				return;
			}else{
				setcookie('favposts', '' , time() + (86400 * 1826.25), '/');
			}
			$i++;
		}
		/*render*/
		foreach(FlatDB::listEntry('topic') as $topic){
			$d[$plugin.'postsLists'][$topic] = (int)$d[$plugin.'postsLists'][$topic];
		}  
		flatDB::saveEntry('plugin', $plugin, $d);
	}
	
}
function favposts_topTopic(){
	global $lang;
	$plugin = 'favposts';
	$d = FlatDB::readEntry('plugin',$plugin);
	$out='';
	
	if($d[$plugin.'state']){
		arsort($d[$plugin.'postsLists']);
		$i=0;
		foreach($d[$plugin.'postsLists'] as $postList=>$view){
			if($i<1&&(int)$view>0&&$postList==$_GET['topic']){
				$out.='<span class="badge bg-warning" data-toggle="tooltip" data-placement="top" title="'.$lang[$plugin.'favlabel'].'"><i class="fa fa-star"></i></span>';
			}
			$i++;
		}
		$d[$plugin.'postsLists'][$_GET['topic']] = (int)($d[$plugin.'postsLists'][$_GET['topic']]+2)-1;
		flatDB::saveEntry('plugin', $plugin, $d);
	}
	
	return $out;
}
function favposts_footerJS(){
		global $lang;
	$plugin = 'favposts';
	$d = FlatDB::readEntry('plugin',$plugin);
	$out='';
	if($d[$plugin.'state']&&isset($_COOKIE['favposts'])){
		$out.='<script plugin-script-name="'.$plugin.'">$(document).ready(function(){
				let favBanner = document.querySelectorAll("[data-original-title=\'Edit\']");for(let i=0;i<favBanner.length;i++){
				
				if(favBanner[i].getAttribute("href").match("'.(isset($_COOKIE['favposts']) ? $_COOKIE['favposts'] : '').'")){
				if(favBanner[i].parentElement.parentElement.querySelector(".d-flex strong")){
					favBanner[i].parentElement.parentElement.querySelector(".d-flex strong").innerHTML += "<span class=\'badge bg-warning\' data-toggle=\'tooltip\' data-placement=\'top\' title=\''.$lang[$plugin.'favlabel'].'\'><i class=\'fa fa-star\'></i></span>";
				}else{
					favBanner[i].parentElement.innerHTML += "<span class=\'badge bg-warning\' data-toggle=\'tooltip\' data-placement=\'top\' title=\''.$lang[$plugin.'favlabel'].'\'><i class=\'fa fa-star\'></i></span>";
				}
				}}});</script>';
	}
	return $out;
}
?>