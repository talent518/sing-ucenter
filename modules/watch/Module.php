<?php
namespace app\modules\watch;

use app\core\CCModule;

/**
 * market module definition class
 */
class Module extends CCModule {
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
