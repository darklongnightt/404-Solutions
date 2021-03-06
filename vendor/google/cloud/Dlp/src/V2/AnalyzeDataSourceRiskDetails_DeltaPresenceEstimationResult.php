<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/privacy/dlp/v2/dlp.proto

namespace Google\Cloud\Dlp\V2;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Result of the δ-presence computation. Note that these results are an
 * estimation, not exact values.
 *
 * Generated from protobuf message <code>google.privacy.dlp.v2.AnalyzeDataSourceRiskDetails.DeltaPresenceEstimationResult</code>
 */
class AnalyzeDataSourceRiskDetails_DeltaPresenceEstimationResult extends \Google\Protobuf\Internal\Message
{
    /**
     * The intervals [min_probability, max_probability) do not overlap. If a
     * value doesn't correspond to any such interval, the associated frequency
     * is zero. For example, the following records:
     *   {min_probability: 0, max_probability: 0.1, frequency: 17}
     *   {min_probability: 0.2, max_probability: 0.3, frequency: 42}
     *   {min_probability: 0.3, max_probability: 0.4, frequency: 99}
     * mean that there are no record with an estimated probability in [0.1, 0.2)
     * nor larger or equal to 0.4.
     *
     * Generated from protobuf field <code>repeated .google.privacy.dlp.v2.AnalyzeDataSourceRiskDetails.DeltaPresenceEstimationResult.DeltaPresenceEstimationHistogramBucket delta_presence_estimation_histogram = 1;</code>
     */
    private $delta_presence_estimation_histogram;

    public function __construct() {
        \GPBMetadata\Google\Privacy\Dlp\V2\Dlp::initOnce();
        parent::__construct();
    }

    /**
     * The intervals [min_probability, max_probability) do not overlap. If a
     * value doesn't correspond to any such interval, the associated frequency
     * is zero. For example, the following records:
     *   {min_probability: 0, max_probability: 0.1, frequency: 17}
     *   {min_probability: 0.2, max_probability: 0.3, frequency: 42}
     *   {min_probability: 0.3, max_probability: 0.4, frequency: 99}
     * mean that there are no record with an estimated probability in [0.1, 0.2)
     * nor larger or equal to 0.4.
     *
     * Generated from protobuf field <code>repeated .google.privacy.dlp.v2.AnalyzeDataSourceRiskDetails.DeltaPresenceEstimationResult.DeltaPresenceEstimationHistogramBucket delta_presence_estimation_histogram = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getDeltaPresenceEstimationHistogram()
    {
        return $this->delta_presence_estimation_histogram;
    }

    /**
     * The intervals [min_probability, max_probability) do not overlap. If a
     * value doesn't correspond to any such interval, the associated frequency
     * is zero. For example, the following records:
     *   {min_probability: 0, max_probability: 0.1, frequency: 17}
     *   {min_probability: 0.2, max_probability: 0.3, frequency: 42}
     *   {min_probability: 0.3, max_probability: 0.4, frequency: 99}
     * mean that there are no record with an estimated probability in [0.1, 0.2)
     * nor larger or equal to 0.4.
     *
     * Generated from protobuf field <code>repeated .google.privacy.dlp.v2.AnalyzeDataSourceRiskDetails.DeltaPresenceEstimationResult.DeltaPresenceEstimationHistogramBucket delta_presence_estimation_histogram = 1;</code>
     * @param \Google\Cloud\Dlp\V2\AnalyzeDataSourceRiskDetails_DeltaPresenceEstimationResult_DeltaPresenceEstimationHistogramBucket[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setDeltaPresenceEstimationHistogram($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Cloud\Dlp\V2\AnalyzeDataSourceRiskDetails_DeltaPresenceEstimationResult_DeltaPresenceEstimationHistogramBucket::class);
        $this->delta_presence_estimation_histogram = $arr;

        return $this;
    }

}

