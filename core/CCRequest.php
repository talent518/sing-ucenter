<?php

namespace app\core;

use yii\helpers\VarDumper;
use yii\log\Logger;

class CCRequest
{
    const CONTENT_TYPE_FORM = 'application/x-www-form-urlencoded';
    const CONTENT_TYPE_JSON = 'application/json';

    const RESPONSE_TYPE_RAW = 1;
    const RESPONSE_TYPE_JSON = 2;

    private $url;
    private $params;
    private $body;
    private $method;
    private $headers;
    private $contentType;
    private $responseType;

    public function __construct($url, $method, array $params = [], array $headers = [],
                                array $body = [], $contentType = self::CONTENT_TYPE_FORM,
                                $responseType = self::RESPONSE_TYPE_RAW)
    {
        $this->url = $url;
        $this->method = strtoupper($method);
        $this->params = $params;
        $this->body = $body;
        $this->headers = $headers;
        $this->contentType = $contentType;
        $this->headers['Content-Type'] = $contentType;
        $this->responseType = $responseType;
    }

    public function send()
    {
        $logData = [
            'url' => $this->url,
            'method' => $this->method,
            'req_headers' => VarDumper::export($this->headers),
            'req_params' => VarDumper::export($this->params),
            'req_body' => VarDumper::export($this->body),
            'slow' => false,
        ];
        $logLevel = Logger::LEVEL_INFO;
        try {
            $beginTime = microtime(true);
            $ch = curl_init();
            if (!empty($this->params)) {
                $this->url .= '?' . self::buildQuery($this->params);
            }
            $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HEADER => false,
                CURLOPT_NOBODY => false,
                CURLOPT_CUSTOMREQUEST => $this->method,
                CURLOPT_URL => $this->url
            ];
            $headers = [];
            foreach ($this->headers as $key => $val)
                array_push($headers, "$key: $val");
            $options[CURLOPT_HTTPHEADER] = $headers;
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
            if (!empty($this->body)) {
                if ($this->contentType == self::CONTENT_TYPE_FORM) {
                    $options[CURLOPT_POSTFIELDS] = self::buildPost($this->body);
                } elseif ($this->contentType == self::CONTENT_TYPE_JSON) {
                    $options[CURLOPT_POSTFIELDS] = self::buildJsonPost($this->body);
                }
            }
            curl_setopt_array($ch, $options);
            $ret = curl_exec($ch);
            $logData['response_body'] = VarDumper::export($ret);
            $logData['response_code'] = -1;
            if ($ret === false) {
                throw new \Exception(curl_error($ch));
            }
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $logData['response_code'] = $responseCode;
            if ($responseCode !== 200)
                throw new \Exception(VarDumper::export($ret));
            $costTime = microtime(true) - $beginTime;
            if ($costTime >= \Yii::$app->params['execute_slow_time']) {
                $logData['slow'] = true;
                $logLevel = Logger::LEVEL_WARNING;
            }
            $logData['cost_time'] = microtime(true) - $beginTime;
            return $this->formatResponse($ret);
        } catch (\Exception $ex) {
            $logData['error'] = $ex->getMessage();
            $logLevel = Logger::LEVEL_ERROR;
            throw $ex;
        } finally {
            if (isset($ch))
                curl_close($ch);
            if ($logLevel === Logger::LEVEL_INFO) {
                \Yii::info($logData, 'curl');
            } elseif ($logLevel === Logger::LEVEL_WARNING) {
                \Yii::warning($logData, 'curl');
            } elseif ($logLevel === Logger::LEVEL_ERROR) {
                \Yii::error($logData, 'curl');
            }
        }
    }

    private function buildQuery($params, $numericPrefix = '', $argSeparator = '&', $prefixKey = '')
    {
        $str = '';
        foreach ($params as $key => $val) {
            if (!is_array($val)) {
                $str .= $argSeparator;
                if ($prefixKey === '') {
                    if (is_int($key)) {
                        $str .= $numericPrefix;
                    }
                    $str .= urlencode($key) . '=' . urlencode($val);
                } else {
                    $str .= urlencode($prefixKey) . '=' . urlencode($val);
                }
            } else {
                if ($prefixKey == '') {
                    $prefixKey .= $key;
                }
                $prefixKey .= '[]';
                if (is_array($val[0])) {
                    $arr = array();
                    $arr[$key] = $val[0];
                    $str .= $argSeparator . http_build_query($arr);
                } else {
                    $str .= $argSeparator . $this->buildQuery($val, $numericPrefix, $argSeparator, $prefixKey);
                }
                $prefixKey = '';
            }
        }
        return substr($str, strlen($argSeparator));
    }

    private function buildPost($params, $numericPrefix = '', $argSeparator = '&', $prefixKey = '')
    {
        $str = '';
        foreach ($params as $key => $val) {
            if (!is_array($val)) {
                $str .= $argSeparator;
                if ($prefixKey === '') {
                    if (is_int($key)) {
                        $str .= $numericPrefix;
                    }
                    $str .= $key . '=' . urlencode($val);
                } else {
                    $str .= $prefixKey . '=' . urlencode($val);
                }
            } else {
                if ($prefixKey === '') {
                    $prefixKey .= $key;
                }
                $prefixKey .= '[]';
                $str .= $argSeparator . $this->buildPost($val, $numericPrefix, $argSeparator, $prefixKey);
                $prefixKey = '';
            }
        }
        return substr($str, strlen($argSeparator));
    }

    private function buildJsonPost($params)
    {
        return json_encode($params);
    }

    private function formatResponse($response)
    {
        if ($this->responseType == self::RESPONSE_TYPE_JSON)
            return json_decode($response);
        return $response;
    }
}