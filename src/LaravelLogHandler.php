<?php

namespace AliyunPHPLog;

use AliyunPHPLog\Lib\Client;
use AliyunPHPLog\Lib\LogItem;
use AliyunPHPLog\Lib\PutLogsRequest;
use Monolog\Logger;
use Monolog\Handler\AbstractSyslogHandler;

class LaravelLogHandler extends AbstractSyslogHandler
{
    protected $client;

    protected $ident = 'php';

    protected $project;

    protected $logstore;


    public function __construct($endpoint, $accessKeyId, $accessKey, $project, $logstore)
    {
        parent::__construct($facility = LOG_USER, $level = Logger::DEBUG, $bubble = true);
        $this->project  = $project;
        $this->logstore  = $logstore;
        $this->client = new Client($endpoint, $accessKeyId, $accessKey);
    }

    protected function write(array $record): void
    {
        $lines = $this->splitMessageIntoLines($record['formatted']);
        $topic = 'project-log';
        $logItem = new LogItem();
        $logItem->setTime(time());
        $logItem->setContents($lines);
        $logitems = array($logItem);
        $request = new PutLogsRequest($this->project, $this->logstore, $topic, null, $logitems);

        $response = $this->client->putLogs($request);
    }

    public function close(): void
    {
        $this->client = null;
    }

    private function splitMessageIntoLines($message)
    {
        if (is_array($message)) {
            $message = implode("\n", $message);
        }

        return preg_split('/$\R?^/m', $message, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Make common syslog header (see rfc5424)
     */
    protected function makeCommonSyslogHeader($severity)
    {
        $priority = $severity + $this->facility;

        if (!$pid = getmypid()) {
            $pid = '-';
        }

        if (!$hostname = gethostname()) {
            $hostname = '-';
        }

        return "<$priority>1 " .
            $this->getDateTime() . " " .
            $hostname . " " .
            $this->ident . " " .
            $pid . " - - ";
    }

    protected function getDateTime()
    {
        return date(\DateTime::RFC3339);
    }
}