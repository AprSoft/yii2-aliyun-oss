<?php

namespace AprSoft\Aliyun\OSS;

use OSS\OssClient;
use yii\base\Component;

class Bucket extends Component
{
    protected $ossClient;

    protected $bucket;

    public function __construct($accessKeyId, $accessKeySecret, $endpoint, $isCName = false, $securityToken = null)
    {
        $this->ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, $isCName, $securityToken);
    }
    public function create($bucket, $acl = OssClient::OSS_ACL_TYPE_PRIVATE, $options = null)
    {
        return $this->ossClient->createBucket($bucket, $acl, $options);
    }

    public function delete($bucket, $options = null)
    {
        return $this->ossClient->deleteBucket($bucket, $acl, $options);
    }

    public function exist($bucket)
    {
        return $this->ossClient->doesBucketExist($bucket);
    }

    public function getAcl($bucket, $options = null)
    {
        return $this->ossClient->doesBucketExist($bucket);
    }

    public function setAcl($bucket, $acl, $options = null)
    {
        return $this->ossClient->putBucketAcl($bucket, $acl, $options);
    }

}
