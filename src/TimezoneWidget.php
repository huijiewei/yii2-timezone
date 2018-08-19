<?php
/**
 * Created by PhpStorm.
 * User: huijiewei
 * Date: 5/26/15
 * Time: 23:25
 */

namespace huijiewei\timezone;

use huijiewei\select2\Select2Widget;
use yii\base\InvalidArgumentException;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;
use yii\web\AssetBundle;
use yii\web\JsExpression;

class TimezoneWidget extends Widget
{
    public $name;
    public $value;

    public $label = '请选择时区';
    public $language = 'zh_CN';
    public $getCurrentTimeAjaxUrl = '';
    public $getCurrentTimeMessage = '正在获取时间...';

    public $onChange;

    public $auto = true;

    /* @var $_assetBundle AssetBundle */
    private $_assetBundle;

    public function init()
    {
        parent::init();

        if (empty($this->getCurrentTimeAjaxUrl)) {
            throw new InvalidArgumentException('请先设置 getCurrentTimeAjaxUrl');
        }

        $this->registerAssetBundle();
        $this->registerJavascript();
    }

    public function registerAssetBundle()
    {
        $this->_assetBundle = TimezoneAsset::register($this->getView());
    }

    public function registerJavascript()
    {
        $ajaxUrl = $this->getCurrentTimeAjaxUrl;

        $autoDeter = $this->auto ? 'true' : 'false';

        $js = 'var onChange = null;';

        if (strlen($this->onChange)) {
            $js = 'onChange = function(req) { ' . new JsExpression($this->onChange) . '  };';
        }

        $this->getView()->registerJs($js);

        $js = <<<EOD
        var s2 = $('#{$this->id}');
        var hp = s2.closest('.select2-wrap').next('.help-block');

        function getCurrentTime(timezone) {
            hp.find('.fa').removeClass('fa-clock-o fa-info-circle').addClass('fa-spinner fa-pulse');
            hp.find('.time').html('{$this->getCurrentTimeMessage}');

            $.getJSON('{$ajaxUrl}', {timezone:timezone} ,function(req) {
                hp.find('.fa').removeClass('fa-spinner fa-pulse').addClass('fa-clock-o');
                var fmt = req.format;
                var time = moment(req.time);
                hp.find('.time').html(time.format(fmt));

                setInterval(function() {
                    time.add(60, 'seconds');
                    hp.find('.time').html(time.format(fmt));
                    return true;
                }, 60000);

                if(onChange != null) {
                    onChange(req);
                }
            });
        }

        s2.on('change',function(){
            getCurrentTime(s2.val());
        });

        if(s2.val().length == 0) {
            var auto = {$autoDeter};
            if(auto) {
                var tz = jstz.determine();
                s2.val(tz.name).trigger('change');
            }
        } else {
            getCurrentTime(s2.val());
        }
EOD;

        $this->getView()->registerJs($js);
    }

    public function run()
    {
        echo Select2Widget::widget([
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'data' => ArrayHelper::merge(['' => ''], TimezoneHelper::getTimeZoneArray()),
            'clientOptions' => [
                'language' => $this->language,
                'placeholder' => $this->label,
            ],
        ]);

        echo '<span class="help-block"><i class="fa fa-fw fa-info-circle"></i><span class="time">' . $this->label . '</span></span>';
    }

    public function getAssetBundle()
    {
        if (!($this->_assetBundle instanceof AssetBundle)) {
            $this->registerAssetBundle();
        }

        return $this->_assetBundle;
    }
}
