<?php
namespace AprSoft\Aliyun\OSS;

use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * @todo Aliyun 图片上传
 */
class Image extends Component
{

    // OSS获得的AccessKeyId
    public $accessKeyId;
    // OSS获得的AccessKeySecret
    public $accessKeySecret;
    // OSS获得的bucket
    public $bucket;
    //OSS数据中心访问域名，例如oss-cn-hangzhou.aliyuncs.com
    public $endpoint;
    //是否对Bucket做了域名绑定，并且Endpoint参数填写的是自己的域名
    public $isCName = false;

    public $securityToken = null;

    private $_ossClient;

    public function __construct()
    {
        parent::init();
        if (!isset($this->accessKeyId)) {
            throw new InvalidConfigException('请先配置Access Key');
        }

        if (!isset($this->accessKeySecret)) {
            throw new InvalidConfigException('请先配置accessKeySecret');
        }

        if (!isset($this->endpoint)) {
            throw new InvalidConfigException('请先配置endpoint');
        }

        if (!isset($this->bucket)) {
            throw new InvalidConfigException('请先配置bucket');
        }
    }

    public function getClient()
    {
        if ($this->_ossClient === null) {
            $this->setClient(new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint, $this->isCName, $this->securityToken));
        }
        return $this->_ossClient;
    }

    public function setClient(OssClient $ossClient)
    {
        $this->_ossClient = $ossClient;
    }

    public function upload($name, $filePath, $options = null)
    {
        return $this->client->uploadFile($this->bucket, $name, $filePath, $options);
    }

    public function multipartUpload($name, $file, $options = null)
    {
        return $this->client->multiuploadFile($this->bucket, $name, $filePath, $options);
    }

    public function exist($name, $options = null)
    {
        return $this->client->doesObjectExist($this->bucket, $name);
    }

    public function download()
    {

    }

}
