<?php
namespace AprSoft\Aliyun\OSS;

use OSS\Core\OssException;
use Yii;
use yii\base\Component;

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
    /**
     * @todo    上传文件
     * @param   [string]    $name     [存储的文件标识]
     * @param   [string]    $filePath [本地文件路径]
     * @param   [array]     $options  [上传选项]
     * @return  [boolean]
     */
    public function upload($name, $filePath, $options = null)
    {
        try {
            $this->client->uploadFile($this->bucket, $name, $filePath, $options);
        } catch (OssException $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
        return true;
    }

    /**
     * @todo    分片上传文件
     * @param   [string]    $name     [存储的文件标识]
     * @param   [string]    $filePath [本地文件路径]
     * @param   [array]     $options  [上传选项]
     * @return  [boolean]
     */
    public function multipartUpload($name, $file, $options = null)
    {
        try {
            $this->client->multiuploadFile($this->bucket, $name, $filePath, $options);
        } catch (OssException $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
        return true;
    }

    /**
     * @todo    文件是否存在
     * @param   [string]    $name     [存储的文件标识]
     * @param   [array]     $options  [上传选项]
     * @return  [boolean]
     */
    public function exist($name, $options = null)
    {
        try {
            $this->client->doesObjectExist($this->bucket, $name);
        } catch (OssException $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
        return true;
    }

    /**
     * @todo    获取指定资源的名称
     * @param   [string]    $name     [存储的文件标识]
     * @param   [array]     $options  [上传选项]
     * @return  [boolean]
     */
    public function get($name, $options = null)
    {
        try {
            $this->client->getObject($this->bucket, $name, $options);
        } catch (OssException $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
        return true;
    }

}
