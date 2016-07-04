<?php
class MyConfig
{
    public static function read($filename)
    {
		if(!is_file($filename)){
			// Create a settings file for the first time
			file_put_contents($filename, "<?php return array (
  'en_gram' => '',
  'en_lm' => '',
  'lv_gram' => '',
  'lv_lm' => '',
  'de_gram' => '',
  'de_lm' => '',
  'fr_gram' => '',
  'fr_lm' => '',
  'google_key' => '',
  'yandex_key' => '',
  'bing_id' => '',
  'bing_se' => '',
  'hugo_pw' => '',
  'hugo_em' => '',
) ;");
		}
        $config = include $filename;
        return $config;
    }
    public static function write($filename, array $config)
    {
        $config = var_export($config, true);
        file_put_contents($filename, "<?php return $config ;");
    }
}

if(count($_GET) > 1) //If settings saved
{
	$config['en_gram'] 		= $_GET['en_gram'];
	$config['en_lm'] 		= $_GET['en_lm'];
	$config['lv_gram'] 		= $_GET['lv_gram'];
	$config['lv_lm'] 		= $_GET['lv_lm'];
	$config['de_gram'] 		= $_GET['de_gram'];
	$config['de_lm'] 		= $_GET['de_lm'];
	$config['fr_gram'] 		= $_GET['fr_gram'];
	$config['fr_lm'] 		= $_GET['fr_lm'];
	$config['google_key'] 	= $_GET['google_key'];
	$config['yandex_key'] 	= $_GET['yandex_key'];
	$config['bing_id'] 		= $_GET['bing_id'];
	$config['bing_se'] 		= $_GET['bing_se'];
	$config['hugo_pw'] 		= $_GET['hugo_pw'];
	$config['hugo_em'] 		= $_GET['hugo_em'];
	MyConfig::write('include/settings.php', $config);
}
$config = MyConfig::read('include/settings.php');
$en_gram 	= $config['en_gram'];
$en_lm 		= $config['en_lm'];
$lv_gram 	= $config['lv_gram'];
$lv_lm 		= $config['lv_lm'];
$de_gram 	= $config['de_gram'];
$de_lm 		= $config['de_lm'];
$fr_gram 	= $config['fr_gram'];
$fr_lm 		= $config['fr_lm'];
$google_key = $config['google_key'];
$yandex_key = $config['yandex_key'];
$bing_id 	= $config['bing_id'];
$bing_se 	= $config['bing_se'];
$hugo_pw 	= $config['hugo_pw'];
$hugo_em 	= $config['hugo_em'];
?>
﻿<form action="?">
<h3>APIs</h3>
	<div class="api">
		<b>Google Translate</b><br style="clear: both;"/>
		<div style="float: left; margin-left:5px;">
			Google Translate Key:
			<input style="width:350px;" class="form-control" name="google_key" value="<?php echo $google_key;?>"/>
		</div>
		<br style="clear: both;"/><br/>
	</div>
	<div class="api">
		<b>Yandex Translate</b><br style="clear: both;"/>
		<div style="float: left; margin-left:5px;">
			Yandex API Key:
			<input style="width:350px;" class="form-control" name="yandex_key" value="<?php echo $yandex_key;?>"/>
		</div>
		<br style="clear: both;"/><br/>
	</div>
	<div class="api">
		<b>Bing Translator</b><br style="clear: both;"/>
		<div style="float: left; margin-left:5px;">
			Bing Client ID:<br/>
			<input style="width:350px;" class="form-control" name="bing_id" value="<?php echo $bing_id;?>"/>
		</div>
		<div style="float: left; margin-left:5px;">
			Bing Client Secret:<br/>
			<input style="width:350px;" class="form-control" name="bing_se" value="<?php echo $bing_se;?>"/>
		</div>
		<br style="clear: both;"/><br/>
	</div>
	<div class="api">
		<b>Hugo</b><br style="clear: both;"/>
		<div style="float: left; margin-left:5px;">
			Email:<br/>
			<input style="width:350px;" class="form-control" name="hugo_pw" value="<?php echo $hugo_pw;?>"/>
		</div>
		<div style="float: left; margin-left:5px;">
			Password:<br/>
			<input type="password" style="width:350px;" class="form-control" name="hugo_em" value="<?php echo $hugo_em;?>"/>
		</div>
		<br style="clear: both;"/>
	</div>
<h3>Languages</h3>
	<div class="language">
		<b>English</b><br style="clear: both;"/>
		<div style="float: left; margin-left:5px;">
			Parse grammar:<br/>
			<input style="width:350px;" class="form-control" name="en_gram" value="<?php echo $en_gram;?>"/>
		</div>
		<div style="float: left; margin-left:5px;">
			Language model:<br/>
			<input style="width:350px;" class="form-control" name="en_lm" value="<?php echo $en_lm;?>"/>
		</div>
		<br style="clear: both;"/><br/>
	</div>
	<div class="language">
		<b>Latvian</b><br style="clear: both;"/>
		<div style="float: left; margin-left:5px;">
			Parse grammar:<br/>
			<input style="width:350px;" class="form-control" name="lv_gram" value="<?php echo $lv_gram;?>"/>
		</div>
		<div style="float: left; margin-left:5px;">
			Language model:<br/>
			<input style="width:350px;" class="form-control" name="lv_lm" value="<?php echo $lv_lm;?>"/>
		</div>
		<br style="clear: both;"/><br/>
	</div>
	<div class="language">
		<b>German</b><br style="clear: both;"/>
		<div style="float: left; margin-left:5px;">
			Parse grammar:<br/>
			<input style="width:350px;" class="form-control" name="de_gram" value="<?php echo $de_gram;?>"/>
		</div>
		<div style="float: left; margin-left:5px;">
			Language model:<br/>
			<input style="width:350px;" class="form-control" name="de_lm" value="<?php echo $de_lm;?>"/>
		</div>
		<br style="clear: both;"/><br/>
	</div>
	<div class="language">
		<b>French</b><br style="clear: both;"/>
		<div style="float: left; margin-left:5px;">
			Parse grammar:<br/>
			<input style="width:350px;" class="form-control" name="fr_gram" value="<?php echo $fr_gram;?>"/>
		</div>
		<div style="float: left; margin-left:5px;">
			Language model:<br/>
			<input style="width:350px;" class="form-control" name="fr_lm" value="<?php echo $fr_lm;?>"/>
		</div>
		<br style="clear: both;"/>
	</div>

	<br style="clear: both;"/>
	<input type="hidden" name="id" value="settings"/>
	<input style="margin-left:5px;" type="submit" class="btn btn-sm btn-default" value="Save settings"/>
</form>
<br/><br/>