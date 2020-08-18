<?php
namespace app\modules\watch;

/**
 * market module definition class
 */
class Module extends \yii\base\Module {
	public $autoRegisterRouters;
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 */
	public $controllerNamespace = 'app\modules\watch\controllers';

	/**
	 *
	 * {@inheritdoc}
	 *
	 */
	public function init() {
		parent::init();
		
		// custom initialization code goes here
	}

}
