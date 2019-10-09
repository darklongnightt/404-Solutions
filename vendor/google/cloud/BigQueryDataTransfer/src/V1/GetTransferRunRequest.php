<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/bigquery/datatransfer/v1/datatransfer.proto

namespace Google\Cloud\BigQuery\DataTransfer\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * A request to get data transfer run information.
 *
 * Generated from protobuf message <code>google.cloud.bigquery.datatransfer.v1.GetTransferRunRequest</code>
 */
class GetTransferRunRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * The field will contain name of the resource requested, for example:
     * `projects/{project_id}/transferConfigs/{config_id}/runs/{run_id}`
     *
     * Generated from protobuf field <code>string name = 1;</code>
     */
    private $name = '';

    public function __construct() {
        \GPBMetadata\Google\Cloud\Bigquery\Datatransfer\V1\Datatransfer::initOnce();
        parent::__construct();
    }

    /**
     * The field will contain name of the resource requested, for example:
     * `projects/{project_id}/transferConfigs/{config_id}/runs/{run_id}`
     *
     * Generated from protobuf field <code>string name = 1;</code>
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * The field will contain name of the resource requested, for example:
     * `projects/{project_id}/transferConfigs/{config_id}/runs/{run_id}`
     *
     * Generated from protobuf field <code>string name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->name = $var;

        return $this;
    }

}

