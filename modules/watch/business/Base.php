<?php
namespace app\modules\watch\business;

use app\core\CCResponse;
use yii\base\Model;
use yii\db\BaseActiveRecord;
use yii\db\Query;

class Base {
	
	private static $params = [];
	
	protected function getParam($key, $def = null) {
		if(array_key_exists($key, self::$params)) {
			return self::$params[$key];
		}
		$request = \Yii::$app->request;
		return self::$params[$key] = $request->getBodyParam($key, $request->get($key, $def));
	}
	
	protected function multiPage(Query $query) {
		$count = ($query ? $query->count() : 0);
		$pageSize = max((int) $this->getParam('pageSize', 10), 1);
		$totalPage = max(1, ceil($count / $pageSize));
		$curPage = max(min((int) $this->getParam('pageIndex'), $totalPage), 1);
		
		if($this->getParam('scene', 'list') !== 'list') {
			return $query ? ['items'=>$query->all()] : ['items'=>[]];
		}
		
		$offset = ($curPage - 1) * $pageSize;
		$ret = [
			'items' => ($query ? $query->offset($offset)->limit($pageSize)->all() : []),
			'total' => (int) $count
		];
		return $ret;
	}
	
	protected function sortByModel(string $class, array $ids, string $priKey = 'id', string $sortKey = 'sort') {
		$transaction = $class::getDb()->beginTransaction();
		try {
			$rows = $class::find()->where([$priKey=>$ids])->indexBy($priKey)->all();
			foreach($ids as $i => $id) {
				if(isset($rows [$id])) {
					$rows [$id]->updateAttributes([
						$sortKey => $i
					]);
				}
			}
			$transaction->commit();
			return $this->asOK('保存排序成功');
		} catch(\Throwable $e) {
			$transaction->rollBack();
			return $this->asError('保存排序错误: ' . $e->getMessage());
		}
	}
	
	protected function deleteByModel(BaseActiveRecord $model) {
		if($model->delete()) {
			return $this->asOK('删除成功');
		} else {
			return $this->asError('删除失败');
		}
	}
	
	protected function saveByModel(BaseActiveRecord $model, bool $runValidation = true) {
		if($model->save($runValidation)) {
			return $this->asOK('保存成功');
		} elseif($model->hasErrors()) {
			return new CCResponse($model->errors, 412, '表单验证错误');
		} else {
			return $this->asError('保存失败');
		}
	}
	
	protected function asOK($message) {
		return new CCResponse(null, 0, $message);
	}
	
	protected function asError(string $message, int $code = 417) {
		return new CCResponse(null, $code, $message);
	}
	
	protected function asData($data, $message = 'ok') {
		return new CCResponse($data, 0, $message);
	}
	
	protected function endError(string $message, int $code = 417) {
		\Yii::$app->response->data = $this->asError($message, $code);
		\Yii::$app->end();
	}
}