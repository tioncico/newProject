<?php

namespace EasySwoole\Oss\AliYun\Model;


use EasySwoole\Oss\AliYun\Core\OssException;

/**
 * Class CnameConfig
 * @package EasySwoole\Oss\AliYun\Model
 *
 * TODO: fix link
 * @link http://help.aliyun.com/document_detail/oss/api-reference/cors/PutBucketcors.html
 */
class TaggingConfig implements XmlConfig
{

    const OSS_MAX_RULES = 10;

    private $taggingList = array();

    public function __construct($taggingList = [])
    {
        $this->taggingList = $taggingList;
    }


    public function getTagging()
    {
        return $this->taggingList;
    }


    public function addTagging($key, $value)
    {
        if (count($this->taggingList) >= self::OSS_MAX_RULES) {
            throw new OssException(
                "num of tagging in the config exceeds self::OSS_MAX_RULES: " . strval(self::OSS_MAX_RULES));
        }
        $this->taggingList[$key] = $value;
    }

    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->Cname)) return;
        foreach ($xml->Cname as $entry) {
            $cname = array();
            foreach ($entry as $key => $value) {
                $cname[strval($key)] = strval($value);
            }
            $this->cnameList[] = $cname;
        }
    }

    public function serializeToXml()
    {
        $strXml = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<Tagging>
</Tagging>
EOF;
        $xml = new \SimpleXMLElement($strXml);
        $set = $xml->addChild('TagSet');
        foreach ($this->taggingList as $key => $value) {
            $node = $set->addChild('Tag');
            $node->addChild('Key', $key);
            $node->addChild('Value', $value);
        }
        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }
}
