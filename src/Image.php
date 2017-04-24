<?php
namespace AprSoft\Aliyun\OSS;

use OSS\Core\OssException;
use OSS\OssClient;
use Yii;
use yii\base\Component;
use yii\log\Logger;

/**
 * Class OssClient
 *
 * Object Storage Service(OSS) 的客户端类，封装了用户通过OSS API对OSS服务的各种操作，
 * 用户通过OssClient实例可以进行Object，MultipartUpload, ACL等操作，具体
 * 的接口规则可以参考官方OSS API文档
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

    /**
     * 获取object的ACL属性
     *
     * @param string $name
     * @return string
     */
    public function getAcl(string $name)
    {
        try {
            $acl = $this->client->getObjectAcl($this->bucket, $name);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return $acl;
    }

    /**
     * 设置object的ACL属性
     *
     * @param string $name object名称
     * @param string $acl 读写权限，可选值 ['default', 'private', 'public-read', 'public-read-write']
     * @param array $options
     * @return boolean
     */
    public function setAcl(string $name, string $acl = OssClient::OSS_ACL_TYPE_PRIVATE, array $options = null)
    {
        try {
            $this->client->putObjectAcl($this->bucket, $name, $acl, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return true;
    }

    /**
     * 获取bucket下的object列表
     *
     * @param array $options
     * 其中options中的参数如下
     * $options = array(
     *      'max-keys'  => max-keys用于限定此次返回object的最大数，如果不设定，默认为100，max-keys取值不能大于1000。
     *      'prefix'    => 限定返回的object key必须以prefix作为前缀。注意使用prefix查询时，返回的key中仍会包含prefix。
     *      'delimiter' => 是一个用于对Object名字进行分组的字符。所有名字包含指定的前缀且第一次出现delimiter字符之间的object作为一组元素
     *      'marker'    => 用户设定结果从marker之后按字母排序的第一个开始返回。
     *)
     * 其中 prefix，marker用来实现分页显示效果，参数的长度必须小于256字节。
     * @return ObjectListInfo
     * */
    public function list($options)
    {
        try {
            $content = $this->client->listObjects($this->bucket, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return $content;
    }

}
