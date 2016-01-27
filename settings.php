<?php
class MyConfig
{
    public static function read($filename)
    {
        $config = include $filename;
        return $config;
    }
    public static function write($filename, array $config)
    {
        $config = var_export($config, true);
        file_put_contents($filename, "<?php return $config ;");
    }
}

if(count($_GET) > 1) //ja piespiests saglabāt
{
	$config['en_gram'] = $_GET['en_gram'];
	$config['en_lm'] = $_GET['en_lm'];
	$config['lv_gram'] = $_GET['lv_gram'];
	$config['lv_lm'] = $_GET['lv_lm'];
	$config['de_gram'] = $_GET['de_gram'];
	$config['de_lm'] = $_GET['de_lm'];
	$config['fr_gram'] = $_GET['fr_gram'];
	$config['fr_lm'] = $_GET['fr_lm'];
	MyConfig::write('include/settings.php', $config);
}
$config = MyConfig::read('include/settings.php');
$en_gram = $config['en_gram'];
$en_lm = $config['en_lm'];
$lv_gram = $config['lv_gram'];
$lv_lm = $config['lv_lm'];
$de_gram = $config['de_gram'];
$de_lm = $config['de_lm'];
$fr_gram = $config['fr_gram'];
$fr_lm = $config['fr_lm'];
?>
﻿<form action="?">
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
		<br style="clear: both;"/><br/>
	</div>

	<br style="clear: both;"/>
	<input type="hidden" name="id" value="settings"/>
	<input style="margin-left:5px;" type="submit" class="btn btn-sm btn-default" value="Save settings"/>
</form>
