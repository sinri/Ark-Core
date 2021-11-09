<?php

namespace sinri\ark\core\matrix;

use InvalidArgumentException;

/**
 * @since 2.7.22
 */
class ArkMatrix
{
    /**
     * @var array[]
     */
    protected $matrixData = [];
    /**
     * @var string[]
     */
    protected $columnNameList = [];

    /**
     * @param string[] $columnNameList
     * @param array[]|null $matrixData
     */
    public function __construct(array $columnNameList = [], array $matrixData = null)
    {
        $this->columnNameList = $columnNameList;
        if ($matrixData !== null) {
            $this->resetMatrixData($columnNameList, $matrixData);
        }
    }

    /**
     * @param string[] $columnNameList
     * @param array[] $matrixData
     * @return $this
     */
    public function resetMatrixData(array $columnNameList, array $matrixData)
    {
        foreach ($matrixData as $matrixDatum) {
            if (!is_array($matrixDatum)) {
                throw new InvalidArgumentException('matrix data must be array of array');
            }
        }
        $this->matrixData = $matrixData;
        $this->columnNameList = $columnNameList;
        return $this;
    }

    public function fillAllCells($value, int $rows)
    {
        $columns = count($this->columnNameList);
        for ($i = 0; $i < $rows; $i++) {
            if (!isset($this->matrixData[$i])) {
                $this->matrixData[$i] = [];
            }
            for ($j = 0; $j < $columns; $j++) {
                $this->matrixData[$i][$j] = $value;
            }
        }
        return $this;
    }

    public function fillRow($value, int $rowIndex)
    {
        $columns = count($this->columnNameList);
        if (!isset($this->matrixData[$rowIndex])) {
            $this->matrixData[$rowIndex] = [];
        }
        for ($j = 0; $j < $columns; $j++) {
            $this->matrixData[$rowIndex][$j] = $value;
        }
        return $this;
    }

    public function fillColumn($value, int $columnIndex, int $rows)
    {
        for ($i = 0; $i < $rows; $i++) {
            if (!isset($this->matrixData[$i])) {
                $this->matrixData[$i] = [];
            }
            $this->matrixData[$i][$columnIndex] = $value;
        }
        return $this;
    }

    public function fillCell($value, int $rowIndex, int $columnIndex)
    {
        $this->matrixData[$rowIndex][$columnIndex] = $value;
        return $this;
    }

    public function updateRow(array $row, int $rowIndex = null)
    {
        if ($rowIndex !== null) {
            $this->matrixData[$rowIndex] = $row;
        } else {
            $this->matrixData[] = $row;
        }
        return $this;
    }

    public function insertRow(array $row, int $rowIndex = null)
    {
        if ($rowIndex !== null) {
            array_splice($this->matrixData, $rowIndex, 0, [$row]);
        } else {
            array_unshift($this->matrixData, $row);
        }
        return $this;
    }

    public function updateColumn(array $column, int $columnIndex, string $columnName = null)
    {
        if ($columnIndex === null) {
            $columnIndex = count($this->columnNameList);
        }
        foreach ($this->matrixData as $rowIndex => &$matrixDatum) {
            $matrixDatum[$columnIndex] = $column[$rowIndex];
        }
        if ($columnName !== null) {
            $this->columnNameList[$columnIndex] = $columnName;
        }
        return $this;
    }

    public function insertColumn(array $column, int $columnIndex, string $columnName)
    {
        if ($columnIndex == 0) {
            array_unshift($this->columnNameList, $columnName);
            foreach ($this->matrixData as $rowIndex => &$matrixDatum) {
                array_unshift($matrixDatum, $column[$rowIndex]);
            }
        } else {
            array_splice($this->columnNameList, $columnIndex, 0, [$columnName]);
            foreach ($this->matrixData as $rowIndex => &$matrixDatum) {
                array_splice($matrixDatum, $columnIndex, 0, [$column[$rowIndex]]);
            }
        }
        return $this;
    }

    public function updateColumnName(int $columnIndex, string $columnName)
    {
        $this->columnNameList[$columnIndex] = $columnName;
        return $this;
    }

    /**
     * @param int[] $columnIndices
     * @param ArkMatrixColumnCriterion $criteria
     * @return ArkMatrix
     */
    public function getSubMatrix(array $columnIndices, ArkMatrixColumnCriterion $criteria)
    {
        $resultMatrixData = [];
        foreach ($this->matrixData as $rowIndex => $matrixDatum) {
            if ($criteria->computedResult([$matrixDatum, $this->columnNameList])) {
                $row = [];
                foreach ($columnIndices as $columnIndex) {
                    $row[] = $matrixDatum[$columnIndex];
                }
                $resultMatrixData[] = $row;
            }
        }

        $columnNameList = [];
        foreach ($columnIndices as $columnIndex) {
            $columnNameList[] = $this->columnNameList[$columnIndex];
        }

        return (new static())->resetMatrixData($columnNameList, $resultMatrixData);
    }

    public function getHeadRows(int $n, int $offset = 0)
    {
        $result = [];
        for ($i = $offset; $i < $n; $i++) {
            $result[] = $this->matrixData[$i];
        }
        return (new static())->resetMatrixData($this->columnNameList, $result);
    }

    public function getTailRows(int $n, int $offset = 0)
    {
        $result = [];
        for ($i = count($this->matrixData) - $offset - 1; $i < count($this->matrixData) - $n; $i++) {
            $result[] = $this->matrixData[$i];
        }
        return (new static())->resetMatrixData($this->columnNameList, $result);
    }

    public function getRawRow(int $rowIndex)
    {
        return $this->matrixData[$rowIndex];
    }

    public function getRowAsDict(int $rowIndex)
    {
        $row = $this->matrixData[$rowIndex];
        $dict = [];
        foreach ($this->columnNameList as $k => $v) {
            $dict[$v] = $row[$k];
        }
        return $dict;
    }

    public function getRawMatrix()
    {
        return $this->matrixData;
    }

    public function getColumnNameList()
    {
        return $this->columnNameList;
    }

    public function getTotalRows()
    {
        return count($this->matrixData);
    }

    public function getTotalColumns()
    {
        return count($this->columnNameList);
    }

    public function getCell(int $rowIndex, int $columnIndex)
    {
        return $this->matrixData[$rowIndex][$columnIndex];
    }
}