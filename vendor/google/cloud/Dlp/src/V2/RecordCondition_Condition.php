<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/privacy/dlp/v2/dlp.proto

namespace Google\Cloud\Dlp\V2;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The field type of `value` and `field` do not need to match to be
 * considered equal, but not all comparisons are possible.
 * A `value` of type:
 * - `string` can be compared against all other types
 * - `boolean` can only be compared against other booleans
 * - `integer` can be compared against doubles or a string if the string value
 * can be parsed as an integer.
 * - `double` can be compared against integers or a string if the string can
 * be parsed as a double.
 * - `Timestamp` can be compared against strings in RFC 3339 date string
 * format.
 * - `TimeOfDay` can be compared against timestamps and strings in the format
 * of 'HH:mm:ss'.
 * If we fail to compare do to type mismatch, a warning will be given and
 * the condition will evaluate to false.
 *
 * Generated from protobuf message <code>google.privacy.dlp.v2.RecordCondition.Condition</code>
 */
class RecordCondition_Condition extends \Google\Protobuf\Internal\Message
{
    /**
     * Field within the record this condition is evaluated against. [required]
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2.FieldId field = 1;</code>
     */
    private $field = null;
    /**
     * Operator used to compare the field or infoType to the value. [required]
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2.RelationalOperator operator = 3;</code>
     */
    private $operator = 0;
    /**
     * Value to compare against. [Required, except for `EXISTS` tests.]
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2.Value value = 4;</code>
     */
    private $value = null;

    public function __construct() {
        \GPBMetadata\Google\Privacy\Dlp\V2\Dlp::initOnce();
        parent::__construct();
    }

    /**
     * Field within the record this condition is evaluated against. [required]
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2.FieldId field = 1;</code>
     * @return \Google\Cloud\Dlp\V2\FieldId
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Field within the record this condition is evaluated against. [required]
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2.FieldId field = 1;</code>
     * @param \Google\Cloud\Dlp\V2\FieldId $var
     * @return $this
     */
    public function setField($var)
    {
        GPBUtil::checkMessage($var, \Google\Cloud\Dlp\V2\FieldId::class);
        $this->field = $var;

        return $this;
    }

    /**
     * Operator used to compare the field or infoType to the value. [required]
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2.RelationalOperator operator = 3;</code>
     * @return int
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Operator used to compare the field or infoType to the value. [required]
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2.RelationalOperator operator = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setOperator($var)
    {
        GPBUtil::checkEnum($var, \Google\Cloud\Dlp\V2\RelationalOperator::class);
        $this->operator = $var;

        return $this;
    }

    /**
     * Value to compare against. [Required, except for `EXISTS` tests.]
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2.Value value = 4;</code>
     * @return \Google\Cloud\Dlp\V2\Value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Value to compare against. [Required, except for `EXISTS` tests.]
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2.Value value = 4;</code>
     * @param \Google\Cloud\Dlp\V2\Value $var
     * @return $this
     */
    public function setValue($var)
    {
        GPBUtil::checkMessage($var, \Google\Cloud\Dlp\V2\Value::class);
        $this->value = $var;

        return $this;
    }

}

