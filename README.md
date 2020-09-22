## 说明

原来阿里云相关的日志很不好用，不能用composer 管理不方便，所以写了一个composer包进行管理，现在支持框架
- Yii2
- lavarel

## 安装
```
composer require aliyun-sdk-project/php-log
```

## Yii2 配置

```
'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'AliyunPHPLog\Yii2LogTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'config' => [
                        'endpoint' => 'http://cn-hangzhou.log.aliyuncs.com/',
                        'accessKeyId' => '',
                        'accessKey' => '',
                        'project' => '',
                        'logstore' => '',
                    ],
                ],
            ],
        ],
```
project 是创建日志的项目名称。logstore 存储名称

## Lavarel 配置

```
'aliyun' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => AliyunPHPLog\LaravelLogHandler::class,
            'handler_with' => [
                'endpoint' => env('ALIYUN_ENDPOINT', 'http://cn-shenzhen.log.aliyuncs.com/'),
                'accessKeyId' => '',
                'accessKey' => '' ,
                'project' => '',
                'logstore' => '',
            ],
        ],
```
