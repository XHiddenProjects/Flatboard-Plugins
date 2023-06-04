<?php defined('FLATBOARD') or die('Flatboard Community.');

function voice_install()
{
	global $lang;
	$plugin = 'voice';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= false; 
	flatDB::saveEntry('plugin', $plugin, $data);
}
function voice_editor(){
	global $lang;
	$plugin = 'voice';
	$d = FlatDB::readEntry('plugin',$plugin);
	$out='';
	if($d[$plugin.'state']){
		$out.='<div class="input-group"><i class="fa fa-microphone-slash" mic-active="false" onclick="toggleMic(this)" style="font-size:32px;cursor:pointer;"></i></div>';
	}
	return $out;
}
function voice_footerJS(){
		global $lang;
	$plugin = 'voice';
	$d = FlatDB::readEntry('plugin',$plugin);
	$out='';
	if($d[$plugin.'state']){
		$out.='<script plugin-script-name="'.$plugin.'" src="'.HTML_PLUGIN_DIR.$plugin.DS.'js'.DS.$plugin.'.min.js?v='.$lang[$plugin.'version'].'"></script>';
	}
	return $out;
}
?>