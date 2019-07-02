# Ark-Core
The core components of Ark 2.

## ArkHelper

The static helper functions.

Provided PSR-4 (PSR-0 included) Support.

## ArkLogger

An implementation of `Psr\Log\AbstractLogger` (PSR-3) based on file system.

Since 2.2, the rotating style could be designed to time format other than `Y-m-d`, or just `null` for never rotating.

Since 2.3, the logger appends the support for Buffer. 

Now Version 2.3.