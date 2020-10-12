<?php
namespace app\core;

use AliyunMNS\Client;
use AliyunMNS\Requests\PublishMessageRequest;
use yii\base\Component;
use yii\web\Request;

class MsgNotice extends Component {
	public $ssoAppId;
	public $mnsEndPoint;
	public $mnsAccessId;
	public $mnsAccessKey;
	public $mnsTopic;
	
	public function init() {
		parent::init();
		
		if(!$this->ssoAppId) {
			throw new \Exception('MsgNotice property ssoAppId is not settings');
		}
		
		if(!$this->mnsEndPoint) {
			throw new \Exception('MsgNotice property mnsEndPoint is not settings');
		}
		
		if(!$this->mnsAccessId) {
			throw new \Exception('MsgNotice property mnsAccessId is not settings');
		}
		
		if(!$this->mnsAccessKey) {
			throw new \Exception('MsgNotice property mnsAccessKey is not settings');
		}
		
		if(!$this->mnsTopic) {
			throw new \Exception('MsgNotice property mnsTopic is not settings');
		}
	}
	
	public function publish($event_type, $payload, $tag) {
		if(YII_ENV_DEV) {
			return true;
		}
		
		try {
			\Yii::info(compact('event_type', 'payload', 'tag'), 'mns-topic-publish');
			$client = new Client($this->mnsEndPoint, $this->mnsAccessId, $this->mnsAccessKey);
			$res = $client->getTopicRef($this->mnsTopic)->publishMessage(new PublishMessageRequest(base64_encode(json_encode(compact('event_type', 'payload'))), $tag));
			\Yii::info($res, 'mns-topic-publish');
			return true;
		} catch(\Exception $e) {
			\Yii::error($e->getMessage(), 'mns-topic-publish');
			return false;
		}
	}
	
	private function msg($level, $message, $vars, $trace = null) {
		$request = \Yii::$app->request;

		if($request instanceof Request) {
			if(empty($vars)) {
				foreach(['_SERVER','_GET','_POST','_REQUEST','_COOKIE','_ENV','_SESSION'] as $var) {
					$vars[$var] = $GLOBALS[$var] ?? [];
				}
				unset($var);
			}
			
			$url = $request->absoluteUrl;
			$post = $request->getRawBody();
		} else {
			if(empty($vars)) {
				$vars = $_SERVER;
			}
			$url = 'yii ' . implode(' ', array_map('escapeshellarg', $request->getParams()));
			$post = 'console';
		}
		
		if($trace === null) {
			$ts = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			array_pop($ts);
			$trace = implode(PHP_EOL, array_map(function($t) {
				return sprintf('%s:%d %s%s%s', $t['file'], $t['line'], $t['class'], $t['type'], $t['function']);
			}, $ts));
		}
		
		return $this->publish('alert_notice', [
			'appId' => $this->ssoAppId,
			'url' => $url,
			'post' => $post,
			'level' => $level,
			'message' => $message,
			'vars' => $vars,
			'trace' => $trace,
			'time' => microtime(true)
		], 'alert');
	}
	
	public function error(\Throwable $e, array $vars = []) {
		return $this->msg('error', $e->getMessage(), [], $e->getTraceAsString());
	}
	
	public function warning($message, array $vars = [], $trace = null) {
		return $this->msg('warning', $message, $vars);
	}
	
	public function notice($message, array $vars = [], $trace = null) {
		return $this->msg('notice', $message, $vars);
	}
}