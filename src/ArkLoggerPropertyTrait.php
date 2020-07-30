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
        if ($this->logger === null) {
            // @since 2.7.4
            $this->logger = ArkLogger::getDefaultLogger();
        }
        return $this->logger;
    }

    /**
     * @param ArkLogger $logger
     * @return static
     */
    public function setLogger(ArkLogger $logger): self
    {
        $this->logger = $logger;
        return $this;
    }


}