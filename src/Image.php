<?php
namespace AprSoft\Aliyun\OSS;

use OSS\Core\OssException;
use OSS\OssClient;
use Yii;
use yii\base\Component;
use yii\log\Logger;

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
    public function upload(string $name, string $filePath, array $options = null)
    {
        try {
            $this->client->uploadFile($this->bucket, $name, $filePath, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
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
    public function multipartUpload(string $name, string $filePath, array $options = null)
    {
        try {
            $this->client->multiuploadFile($this->bucket, $name, $filePath, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
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
    public function exist(string $name, array $options = null)
    {
        try {
            $this->client->doesObjectExist($this->bucket, $name);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
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
    public function download(string $name, array $options = null)
    {
        try {
            $content = $this->client->getObject($this->bucket, $name, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return $content;
    }

    /**
     * @todo    获取指定资源的名称
     * @param   [string]    $name     [存储的文件标识]
     * @param   [array]     $options  [上传选项]
     * @return  [boolean]
     */
    public function downloadToLocation(string $name, string $location, array $options = null)
    {
        $options[OssClient::OSS_FILE_DOWNLOAD] = $location;
        try {
            $this->client->getObject($this->bucket, $name, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return true;
    }

    /**
     * @todo    复制文件
     * @param   [type] $fromName   [资源名称]
     * @param   [type] $toName     [新资源名称]
     * @param   [type] $options    [选项]
     * @param   [type] $toBucket   [目标Bucket]
     * @param   [type] $fromBucket [来源Bucket]
     * @return  [Boolean]
     */
    public function copy(string $fromName, string $toName, array $options = null, string $toBucket = null, string $fromBucket = null)
    {
        if ($fromBucket == null) {
            $fromBucket = $this->bucket;
        }
        if ($toBucket == null) {
            $toBucket = $this->bucket;
        }
        try {
            $this->client->copyObject($fromBucket, $fromName, $toBucket, $toName, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return true;
    }

    /**
     * @todo    删除资源
     * @param   [string]    $name       [资源名称]
     * @param   [array]     $options    [选项]
     * @return  [boolean]
     */
    public function delete(string $name, array $options = null)
    {
        try {
            $this->client->deleteObject($this->bucket, $name, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return true;
    }

}
