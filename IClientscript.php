<?php
/*
 * Clientscript extension. Adds auto Less compiling to registerCSSFile method
 * Based on http://leafo.net/lessphp
 * orginal url: https://github.com/marvelsrp/LessYiiCClientScript
 * edited by Maxim Bugai
 */
class IClientscript extends CClientScript{

    public $compress = FALSE;


    public function registerLessFile($file_name, $less_subdir = '')
    {
        $protected_less = Yii::getPathOfAlias('webroot').'/protected/less';
        if (!empty($less_subdir)) $protected_less .= '/'.$less_subdir;

        $import_dir = Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('application.assets'));
        if (!empty($less_subdir)) $import_dir .= '/'.$less_subdir;
        $public_link = $import_dir.'/'.$file_name.'.css';
        $public_file =  Yii::getPathOfAlias('webroot').$public_link;

        $cache_path = Yii::getPathOfAlias('webroot').'/protected/runtime/less/';
        if (!empty($less_subdir)) $cache_path .= $less_subdir.'/';
        $cache_file = $cache_path.$file_name.'.cache';


        // create import folder in protected/less
        if (!is_dir(Yii::getPathOfAlias('webroot').$import_dir)) {
            mkdir(Yii::getPathOfAlias('webroot').$import_dir, 0777, true);
        } else if (!is_writable(Yii::getPathOfAlias('webroot').$import_dir)) {
            throw new CException('Clientscript: ' . Yii::getPathOfAlias('webroot').$import_dir . ' is not writable.');
        }

        // create cache folder in protected/runtime
        if (!is_dir($cache_path)) {
            mkdir($cache_path, 0777, true);
        } else if (!is_writable($cache_path)) {
            throw new CException('Clientscript: ' . $cache_path . ' is not writable.');
        }

        // Get cache
        if (file_exists($cache_file)) {
            $cache = unserialize(file_get_contents($cache_file));
        } else {
            $cache = $protected_less.'/'.$file_name.'.less';
        }

        //compile new cache
        try {
            require_once(dirname(__FILE__).'/include/lessc.inc.php');
            $lessc = new lessc;

            if($this->compress){
                $lessc->setFormatter("compressed");
            }
            $newCache = $lessc->cachedCompile($cache);

        } catch (exception $e) {
            throw new CException(__CLASS__.': Failed to compile less file with message: '.$e->getMessage().'.');
        }


        if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {

            file_put_contents($cache_file, serialize($newCache));
            file_put_contents($public_file, $newCache['compiled']);
        }

        return parent::registerCssFile(baseUrl($public_link));
    }

}