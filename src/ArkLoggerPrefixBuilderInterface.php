<?php


namespace sinri\ark\core;


interface ArkLoggerPrefixBuilderInterface
{
    public function buildPrefix(): string;
}