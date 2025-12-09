<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/style.css',
    ];

//    public $js = [
//        'js/script.js',
//    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}