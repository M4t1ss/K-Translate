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