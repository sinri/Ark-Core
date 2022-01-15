<?php

namespace sinri\ark\core\test\cases\matrix;

use PHPUnit\Framework\TestCase;
use sinri\ark\core\matrix\ArkMatrix;
use sinri\ark\core\matrix\ArkMatrixColumnCriterion;

class TestForMatrix1 extends TestCase
{
    public function test1()
    {
        $matrix = new ArkMatrix();
        $matrix->resetMatrixData(
            ['c1', 'c2', 'c3'],
            [
                [1, 2, 3],
                [4, 5, 6],
                [7, 8, 9],
            ]
        );
        self::assertEquals(5, $matrix->getCell(1, 1));

        $matrix->fillCell(0, 1, 1);
        self::assertEquals(0, $matrix->getCell(1, 1));

        $subMatrix = $matrix->getSubMatrix(
            [1],
            ArkMatrixColumnCriterion::not(
                ArkMatrixColumnCriterion::for(1)->lessThan(3)
            )
        );
//        var_dump($subMatrix->getColumnNameList());
//        var_dump($subMatrix->getRawMatrix());
        $this->assertEquals(1, $subMatrix->getTotalRows());
        $this->assertEquals(1, $subMatrix->getTotalColumns());
        $this->assertEquals(8, $subMatrix->getCell(0, 0));

        $matrix->updateColumn([10, 11, 12], 1);
        self::assertEquals(11, $matrix->getCell(1, 1));

        $matrix->insertColumn([20, 21, 22], 1, 'c1x');
        var_dump($matrix->getRawMatrix());
        self::assertEquals(21, $matrix->getCell(1, 1));
        self::assertEquals(11, $matrix->getCell(1, 2));

    }
}