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
 * 用户通过OssClient实例可以进行 Bucket, ACL等操作，具体
 * 的接口规则可以参考官方OSS API文档
 */

class Bucket extends Component
{

    // OSS获得的AccessKeyId
    public $accessKeyId;
    // OSS获得的AccessKeySecret
    public $accessKeySecret;
    // OSS获得的bucket
    public $bucket = null;
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
     * 创建bucket
     *
     * @param string $bucket
     * @param string $acl 默认创建的bucket的ACL是OssClient::OSS_ACL_TYPE_PRIVATE
     *
     * @param array $options
     * @return boolean
     */
    public function create(string $bucket, string $acl = OssClient::OSS_ACL_TYPE_PRIVATE, array $options = null)
    {
        try {
            $this->client->createBucket($bucket, $acl, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return true;
    }

    /**
     * 删除bucket
     * 如果Bucket不为空（Bucket中有Object，或者有分块上传的碎片），则Bucket无法删除，
     * 必须删除Bucket中的所有Object以及碎片后，Bucket才能成功删除。
     *
     * @param string $bucket
     * @param array $options
     * @return boolean
     */
    public function delete(string $bucket, array $options = null)
    {
        try {
            $this->client->deleteBucket($bucket, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return true;
    }

    /**
     * 判断bucket是否存在
     *
     * @param string $bucket
     * @return bool
     */
    public function exist(string $bucket)
    {
        try {
            $this->client->doesBucketExist($bucket);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return true;
    }

    /**
     * 列举用户所有的Bucket[GetService], Endpoint类型为cname不能进行此操作
     *
     * @param array $options
     * @return BucketListInfo
     */
    function list(array $options = null) {
        try {
            $list = $this->client->listBuckets($options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return $list->getBucketList();
    }

    /**
     * 获取bucket的ACL配置情况
     *
     * @param string $bucket
     * @param array $options
     * @return string
     */
    public function getAcl(string $bucket, array $options = null)
    {
        try {
            $acl = $this->client->getBucketAcl($bucket, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return $acl;
    }

    /**
     * 设置bucket的ACL配置情况
     *
     * @param string $bucket bucket名称
     * @param string $acl 读写权限，可选值 ['private', 'public-read', 'public-read-write']
     * @param array $options 可以为空
     * @return boolean
     */
    public function setAcl(string $bucket, string $acl = OssClient::OSS_ACL_TYPE_PRIVATE, array $options = null)
    {
        try {
            $this->client->putBucketAcl($bucket, $acl, $options);
        } catch (OssException $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR, 'oss');
            return false;
        }
        return true;
    }

}
