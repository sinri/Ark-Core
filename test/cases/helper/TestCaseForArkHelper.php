<?php


namespace sinri\ark\core\test\cases\helper;


use PHPUnit\Framework\TestCase;
use sinri\ark\core\ArkHelper;

class TestCaseForArkHelper extends TestCase
{
    protected $targetArray;
    protected $targetObject;

    public function __construct(string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->targetArray = [
            'a' => [
                'a1' => 'a11'
            ],
            'b' => 'c',
            'd' => null,
            'e' => false,
            'f' => 0,
            'g' => 1,
            'h' => '',
        ];
        $this->targetObject = (object)$this->targetArray;
    }

    public function testForReadTargetAsArray()
    {
        $this->assertEquals(
            'a11',
            ArkHelper::readTarget($this->targetArray, ['a', 'a1']),
            __METHOD__
        );
        $this->assertEquals(
            null,
            ArkHelper::readTarget($this->targetArray, ['a', 'a2']),
            __METHOD__
        );
        $this->assertEquals(
            'c',
            ArkHelper::readTarget($this->targetArray, 'b'),
            __METHOD__
        );
        $this->assertEquals(
            null,
            ArkHelper::readTarget($this->targetArray, 'd', false),
            __METHOD__
        );
        $this->assertEquals(
            false,
            ArkHelper::readTarget($this->targetArray, 'e'),
            __METHOD__
        );
        $this->assertEquals(
            false,
            ArkHelper::readTarget($this->targetArray, ['f']),
            __METHOD__
        );
        $this->assertEquals(
            1,
            ArkHelper::readTarget($this->targetArray, ['g']),
            __METHOD__
        );
        $this->assertEquals(
            '',
            ArkHelper::readTarget($this->targetArray, ['h']),
            __METHOD__
        );
    }

    public function testForReadTargetAsObject()
    {
        $this->assertEquals(
            'a11',
            ArkHelper::readTarget($this->targetObject, ['a', 'a1']),
            __METHOD__
        );
        $this->assertEquals(
            null,
            ArkHelper::readTarget($this->targetObject, ['a', 'a2']),
            __METHOD__
        );
        $this->assertEquals(
            'c',
            ArkHelper::readTarget($this->targetObject, 'b'),
            __METHOD__
        );
        $this->assertEquals(
            null,
            ArkHelper::readTarget($this->targetObject, 'd', false),
            __METHOD__
        );
        $this->assertEquals(
            false,
            ArkHelper::readTarget($this->targetObject, 'e'),
            __METHOD__
        );
        $this->assertEquals(
            false,
            ArkHelper::readTarget($this->targetObject, ['f']),
            __METHOD__
        );
        $this->assertEquals(
            1,
            ArkHelper::readTarget($this->targetObject, ['g']),
            __METHOD__
        );
        $this->assertEquals(
            '',
            ArkHelper::readTarget($this->targetObject, ['h']),
            __METHOD__
        );
    }

    public function testForGetFilePathForClassName()
    {
        $path = ArkHelper::getFilePathForClassName(
            ArkHelper::class,
            'sinri\\ark\\core',
            __DIR__ . '/../../../src'
        );
        $this->assertEquals(
            realpath(__DIR__ . '/../../../src/ArkHelper.php'),
            realpath($path),
            __METHOD__
        );
    }
}