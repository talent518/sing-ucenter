<?php


namespace app\modules\open;


use app\core\CCModule;

class Module extends CCModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\open\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}