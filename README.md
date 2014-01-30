yii-less-compiler
=================

yii extension for complile less files add publish to assets 

1. Create folder "protected/extensions/less" and copy into files
2. Paste to "protected/config/main.php":
'components'=>array(
...
        'clientScript' => array(
            'class' => 'application.extensions.less.IClientscript',
        ),
...)
3. Create folder "protected/less" and add here .less files/dir.
4. Use Yii::app()->clientScript->registerLessFile($file_name, $less_subdir='');

P.S. need clear "protected/runtime/less" and "/assets"

For example:

1. protected/less/main.less :
Yii::app()->clientScript->registerLessFile('main');
Publish to "assets/{hash}/main.css"

2. protected/less/auth/login.less :
Yii::app()->clientScript->registerLessFile('login','auth');
Publish to "assets/{hash}/auth/login.css"
