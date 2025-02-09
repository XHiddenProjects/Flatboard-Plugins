<?php defined('FLATBOARD') or die('Flatboard Community.');
/**
 * Security
 *
 * @author 		Gavin
 * @copyright	(c) 2020-2025
 * @license		http://opensource.org/licenses/MIT
 * @package		FlatBoard
 * @version		1.0.0
 * @update		2025-02-09
 */	
/**
 * Pre-installed
 */
function security_install()
{
    $plugin = 'security';
    
    // Check if the plugin is already installed
    if (flatDB::isValidEntry('plugin', $plugin)) {
        return; //void
    }
	
    // Configuration state
    $data = [
        "{$plugin}state" => true,
        "cantDisable"=>true
    ];
    
    // Save configuration
    flatDB::saveEntry('plugin', $plugin, $data);         
}
function security_head(): string{
    global $lang;
    $plugin = 'security';
    $out = '';
    if(flatDB::isValidEntry('plugin',$plugin)){
        $read = flatDB::readEntry('plugin',$plugin);
        if($read["{$plugin}state"]){
            $out.='<link rel="stylesheet" href="'.HTML_PLUGIN_DIR.$plugin.DS.'css'.DS.$plugin.'.css?v='.$lang["{$plugin}version"].'"/>';
            return $out;
        }
    }
    return $out;
}
function security_footer(): string{
    global $lang;
    $plugin = 'security';
    $out = '';
    if(flatDB::isValidEntry('plugin',$plugin)){
        $read = flatDB::readEntry('plugin',$plugin);
        if($read["{$plugin}state"]){
            $out.='<p class="security_alert">'.$lang["{$plugin}alert"].'</p>';
            return $out;
        }
    }
    return $out;
}

function security_footerJS(): string{
    global $lang;
    $plugin = 'security';
    $out='';
    if(flatDB::isValidEntry('plugin',$plugin)){
        $read = flatDB::readEntry('plugin',$plugin);
        if($read["{$plugin}state"]){
            $out.='<script type="module" src="'.HTML_PLUGIN_DIR.$plugin.DS.'js'.DS.$plugin.'.js?v='.$lang["{$plugin}version"].'"></script>';
        }else return $out;
    }return $out;
}
?>