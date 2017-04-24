yii2-aliyun-oss
===============
yii2-aliyun-oss

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist aprsoft/yii2-aliyun-oss "*"
```

or add

```
"aprsoft/yii2-aliyun-oss": "*"
```

to the require section of your `composer.json` file.


Usage
-----

web.php
```php
'aliyunOssImage' => [
    "class" => 'AprSoft\Aliyun\OSS\Image',
    "accessKeyId" => 'xxxxxxxxxxxxxx',
    "accessKeySecret" => 'xxxxxxxx',
    "bucket" => 'xxxxx',
    "endpoint" => 'http://oss-cn-zhangjiakou.aliyuncs.com',
],
'aliyunOssBucket' => [
    "class" => 'AprSoft\Aliyun\OSS\Bucket',
    "accessKeyId" => 'xxxxxxx',
    "accessKeySecret" => 'xxxxxx',
    "endpoint" => 'http://oss-cn-zhangjiakou.aliyuncs.com',
],
```

代码中使用
```php
$bucket = Yii::$app->aliyunOssImage;
$back = $bucket->list();
```
