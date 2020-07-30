<?php


namespace sinri\ark\core;

/**
 * Trait ArkLoggerPropertyTrait
 * @package sinri\ark\core
 * @since 2.7.3
 */
trait ArkLoggerPropertyTrait
{
    /**
     * @var ArkLogger
     */
    protected $logger;

    /**
     * @return ArkLogger
     */
    public function getLogger(): ArkLogger
    {
        return $this->logger;
    }

    /**
     * @param ArkLogger $logger
     * @return ArkLoggerPropertyTrait
     */
    public function setLogger(ArkLogger $logger): ArkLoggerPropertyTrait
    {
        $this->logger = $logger;
        return $this;
    }


}