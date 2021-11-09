<?php

namespace sinri\ark\core\matrix;

use RuntimeException;

class ArkMatrixColumnCriterionUsingName extends ArkMatrixColumnCriterion
{
    public function computedSubjectValue(array $env = null)
    {
        $matrixDatum = $env[0];
        $columnNameList = $env[1];

        $key = array_search($columnNameList, $this->subject);
        if ($key === false) {
            throw new RuntimeException("Column Not Found");
        }
        return $matrixDatum[$key];
    }
}