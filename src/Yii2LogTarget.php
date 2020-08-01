<?php

namespace AliyunPHPLog;

use AliyunPHPLog\Lib\Client;
use AliyunPHPLog\Lib\LogItem;
use AliyunPHPLog\Lib\PutLogsRequest;
use yii\log\LogRuntimeException;
use yii\log\Target;

class Yii2LogTarget extends Target
{
    /**
     * @var object
     */
    protected $client;

    /**
     * @var array
     * 阿里云相关配置参数
     */
    public $config;


    /**
     * {@inheritdoc}
     * @throws
     */
    public function init()
    {
        parent::init();
        if (empty($this->config)) {
            throw new LogRuntimeException('config is must!');
        }

        $this->client = new Client($this->config['endpoint'], $this->config['accessKeyId'], $this->config['accessKey']);

    }

    public function export()
    {
        $topic = 'project-log';
        $logItem = new LogItem();
        $logItem->setTime(time());
        $lines = [];
        foreach ($this->messages as $message) {
            $lines[] = $this->formatMessage($message);
        }
        $logItem->setContents($lines);
        $logitems = array($logItem);
        $request = new PutLogsRequest($this->config['project'], $this->config['logstore'], $topic, null, $logitems);
        $response = $this->client->putLogs($request);
    }
}