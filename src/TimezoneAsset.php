<?php
/**
 * Created by PhpStorm.
 * User: huijiewei
 * Date: 6/4/15
 * Time: 20:57
 */

namespace huijiewei\timezone;

use yii\web\AssetBundle;

class TimezoneAsset extends AssetBundle
{
    public $sourcePath = '@npm/jstz/dist';

    public $js = [
        'jstz.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'huijiewei\moment\MomentAsset',
        'huijiewei\fontawesome\FontAwesomeAsset',
    ];
}
