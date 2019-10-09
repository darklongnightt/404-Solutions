<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/bigtable/v2/bigtable.proto

namespace Google\Cloud\Bigtable\V2;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Response message for Bigtable.CheckAndMutateRow.
 *
 * Generated from protobuf message <code>google.bigtable.v2.CheckAndMutateRowResponse</code>
 */
class CheckAndMutateRowResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Whether or not the request's `predicate_filter` yielded any results for
     * the specified row.
     *
     * Generated from protobuf field <code>bool predicate_matched = 1;</code>
     */
    private $predicate_matched = false;

    public function __construct() {
        \GPBMetadata\Google\Bigtable\V2\Bigtable::initOnce();
        parent::__construct();
    }

    /**
     * Whether or not the request's `predicate_filter` yielded any results for
     * the specified row.
     *
     * Generated from protobuf field <code>bool predicate_matched = 1;</code>
     * @return bool
     */
    public function getPredicateMatched()
    {
        return $this->predicate_matched;
    }

    /**
     * Whether or not the request's `predicate_filter` yielded any results for
     * the specified row.
     *
     * Generated from protobuf field <code>bool predicate_matched = 1;</code>
     * @param bool $var
     * @return $this
     */
    public function setPredicateMatched($var)
    {
        GPBUtil::checkBool($var);
        $this->predicate_matched = $var;

        return $this;
    }

}

