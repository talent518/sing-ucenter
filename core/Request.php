<?php
namespace app\core;

class Request extends \yii\web\Request {
	public function resolve() {
		list($route, $params) = parent::resolve();
		
		return [$route, $params + $this->getQueryParams() + $this->getBodyParams()];
	}
}