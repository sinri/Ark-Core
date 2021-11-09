<?php

namespace sinri\ark\core\matrix;

use sinri\ark\core\entity\ArkCriterion;

/**
 * @since 2.7.22
 */
class ArkMatrixColumnCriterion extends ArkCriterion
{
    public function computedSubjectValue(array $env = null)
    {
        $matrixDatum = $env[0];
        $index = $this->subject;
        return $matrixDatum[$index];
    }
}