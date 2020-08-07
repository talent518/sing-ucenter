<?php
namespace app\core;

class RedisCache extends \yii\redis\Cache {

	public function buildKey($key) {
		return ($_key = $this->isOneArray($key)) !== false ? $_key : ((is_object($key) || is_array($key)) ? md5(serialize($key)) : $key);
	}

	private function isOneArray($array) {
		if(!is_array($array)) {
			return is_object($array) ? false : $array;
		}
		
		$i = 0;
		foreach($array as $key => &$val) {
			if(($i++) !== $key) {
				return false;
			} else {
				$_val = $this->isOneArray($val);
				if($_val === false) {
					return false;
				} elseif(is_array($val)) {
					$val = '[' . $_val . ']';
				} else {
					$val = $_val;
				}
			}
		}
		
		return implode('-', $array);
	}

}
