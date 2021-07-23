<?php


namespace sinri\ark\core\test\cases\logger;


use PHPUnit\Framework\TestCase;
use sinri\ark\core\ArkFSKit;
use sinri\ark\core\ArkLogger;

class TestLoggerFeatureTwoSevenThirteen extends TestCase
{
    protected $logDirPath;

    public function __construct(string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        echo __METHOD__ . PHP_EOL;
        $this->logDirPath = __DIR__ . '/../../log';
    }

    public function testDisabledThisFeature1()
    {
        echo __METHOD__ . PHP_EOL;

        $prefix = 'a/b/c';
        $prefixBuilt = 'a_b_c';
        $expected_dir = $this->logDirPath . DIRECTORY_SEPARATOR . $prefixBuilt;

        $logger = (new ArkLogger($this->logDirPath, $prefix))
            ->setGroupByPrefix(false);
        $logger->notice(__METHOD__);

        $this->assertDirectoryDoesNotExist($expected_dir);
        $files = glob($expected_dir . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            echo __METHOD__ . ' -> ' . $file . PHP_EOL;
            $this->assertFileExists($file);
        }
    }

    public function testDisabledThisFeature2()
    {
        echo __METHOD__ . PHP_EOL;

        $prefix = '';
        $prefixBuilt = 'default-log';
        $expected_dir = $this->logDirPath . DIRECTORY_SEPARATOR . $prefixBuilt;

        $logger = (new ArkLogger($this->logDirPath, $prefix))
            ->setGroupByPrefix(false);
        $logger->notice(__METHOD__);

        $this->assertDirectoryDoesNotExist($expected_dir);
        $files = glob($expected_dir . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            echo __METHOD__ . ' -> ' . $file . PHP_EOL;
            $this->assertFileExists($file);
        }
    }

    public function testEnabledThisFeature1()
    {
        echo __METHOD__ . PHP_EOL;

        $prefix = '';
        $prefixBuilt = 'default-log';
        $expected_dir = $this->logDirPath . DIRECTORY_SEPARATOR . $prefixBuilt;

        $logger = (new ArkLogger($this->logDirPath, $prefix))
            ->setGroupByPrefix(true);
        $logger->notice(__METHOD__);

        $this->assertDirectoryExists($expected_dir);
        $files = glob($expected_dir . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            echo __METHOD__ . ' -> ' . $file . PHP_EOL;
            $this->assertFileExists($file);
        }
    }

    public function testEnabledThisFeature2()
    {
        echo __METHOD__ . PHP_EOL;

        $prefix = '/';
        $prefixBuilt = 'default-log';
        $expected_dir = $this->logDirPath . DIRECTORY_SEPARATOR . $prefixBuilt;

        $logger = (new ArkLogger($this->logDirPath, $prefix))
            ->setGroupByPrefix(true);
        $logger->notice(__METHOD__);
        $this->assertDirectoryExists($expected_dir);
        $files = glob($expected_dir . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            echo __METHOD__ . ' -> ' . $file . PHP_EOL;
            $this->assertFileExists($file);
        }
    }

    public function testEnabledThisFeature3()
    {
        echo __METHOD__ . PHP_EOL;

        $prefix = '//';
        $prefixBuilt = 'default-log';
        $expected_dir = $this->logDirPath . DIRECTORY_SEPARATOR . $prefixBuilt;

        $logger = (new ArkLogger($this->logDirPath, $prefix))
            ->setGroupByPrefix(true);
        $logger->notice(__METHOD__);

        $this->assertDirectoryExists($expected_dir);
        $files = glob($expected_dir . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            echo __METHOD__ . ' -> ' . $file . PHP_EOL;
            $this->assertFileExists($file);
        }
    }

    public function testEnabledThisFeature4()
    {
        echo __METHOD__ . PHP_EOL;

        $prefix = 'a';
        $prefixBuilt = 'a';
        $expected_dir = $this->logDirPath . DIRECTORY_SEPARATOR . $prefixBuilt;

        $logger = (new ArkLogger($this->logDirPath, $prefix))
            ->setGroupByPrefix(true);
        $logger->notice(__METHOD__);

        $this->assertDirectoryExists($expected_dir);
        $files = glob($expected_dir . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            echo __METHOD__ . ' -> ' . $file . PHP_EOL;
            $this->assertFileExists($file);
        }
    }

    public function testEnabledThisFeature5()
    {
        echo __METHOD__ . PHP_EOL;

        $prefix = 'a/b';
        $prefixBuilt = 'a';
        $expected_dir = $this->logDirPath . DIRECTORY_SEPARATOR . $prefixBuilt;

        $logger = (new ArkLogger($this->logDirPath, $prefix))
            ->setGroupByPrefix(true);
        $logger->notice(__METHOD__);

        $this->assertDirectoryExists($expected_dir);
        $files = glob($expected_dir . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            echo __METHOD__ . ' -> ' . $file . PHP_EOL;
            $this->assertFileExists($file);
        }
    }

    public function testEnabledThisFeature6()
    {
        echo __METHOD__ . PHP_EOL;

        $prefix = 'a/b/c';
        $prefixBuilt = 'a' . DIRECTORY_SEPARATOR . 'b';
        $expected_dir = $this->logDirPath . DIRECTORY_SEPARATOR . $prefixBuilt;

        $logger = (new ArkLogger($this->logDirPath, $prefix))
            ->setGroupByPrefix(true);
        $logger->notice(__METHOD__);

        $this->assertDirectoryExists($expected_dir);
        $files = glob($expected_dir . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            echo __METHOD__ . ' -> ' . $file . PHP_EOL;
            $this->assertFileExists($file);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        ArkFSKit::deleteAnything($this->logDirPath);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        echo __METHOD__ . PHP_EOL;
        ArkFSKit::deleteAnything($this->logDirPath);
    }
}