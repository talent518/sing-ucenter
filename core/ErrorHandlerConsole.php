<?php
namespace app\core;

use yii\console\ErrorHandler;
use app\jobs\MsgNoticeJob;

class ErrorHandlerConsole extends ErrorHandler {
	protected function renderException($exception) {
		\Yii::$app->get('msgNotice')->error($exception);
		
		parent::renderException($exception);
	}
}