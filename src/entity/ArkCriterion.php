<?php

namespace sinri\ark\core\entity;
/**
 *
 * @since 2.7.22
 */
class ArkCriterion
{
    // for scalar
    const JUDGE_METHOD_EQUAL = '==';
    const JUDGE_METHOD_NOT_EQUAL = '!=';
    const JUDGE_METHOD_LESS_THAN = '<';
    const JUDGE_METHOD_LESS_THAN_OR_EQUAL = '<=';
    const JUDGE_METHOD_GREATER_THAN = '>';
    const JUDGE_METHOD_GREATER_THAN_OR_EQUAL = '>=';
    // for type same equal
    const JUDGE_METHOD_IS = '===';
    // for array
    const JUDGE_METHOD_INSIDE = 'inside'; // subject in an array as standard
    const JUDGE_METHOD_OUTSIDE = 'outside';// subject not in an array as standard
    // for string
    const JUDGE_METHOD_CONTAINS = 'contains';
    const JUDGE_METHOD_STARTS_WITH = 'prefixing';
    const JUDGE_METHOD_ENDS_WITH = 'suffixing';
    // for logic
    const JUDGE_METHOD_NOT = '!';
    const JUDGE_METHOD_AND = '&&';
    const JUDGE_METHOD_OR = '||';

    protected $subject;
    /**
     * @var string
     */
    protected $judgeMethod;
    protected $standard;

    public function __construct($subject, string $judgeMethod, $standard = null)
    {
        $this->subject = $subject;
        $this->judgeMethod = $judgeMethod;
        $this->standard = $standard;
    }

    public static function for($subject)
    {
        return new static($subject, '');
    }

    /**
     * @param ArkCriterion $criterion
     * @return $this
     */
    public static function not($criterion)
    {
        $x = new static(null, '');
        $x->judgeMethod = self::JUDGE_METHOD_NOT;
        $x->standard = $criterion;
        return $x;
    }

    /**
     * @param ArkCriterion[] $criteria
     * @return $this
     */
    public static function allTrue(array $criteria)
    {
        $x = new static(null, '');
        $x->judgeMethod = self::JUDGE_METHOD_AND;
        $x->standard = $criteria;
        return $x;
    }

    /**
     * @param ArkCriterion[] $criteria
     * @return $this
     */
    public static function anyTrue(array $criteria)
    {
        $x = new static(null, '');
        $x->judgeMethod = self::JUDGE_METHOD_OR;
        $x->standard = $criteria;
        return $x;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getJudgeMethod(): string
    {
        return $this->judgeMethod;
    }

    /**
     * @return mixed|null
     */
    public function getStandard()
    {
        return $this->standard;
    }

    public function computedResult(array $env = null): bool
    {
        switch ($this->judgeMethod) {
            case self::JUDGE_METHOD_EQUAL:
                return $this->computedSubjectValue($env) == $this->computedStandardValue($env);
            case self::JUDGE_METHOD_NOT_EQUAL:
                return $this->computedSubjectValue($env) != $this->computedStandardValue($env);
            case self::JUDGE_METHOD_LESS_THAN:
                return $this->computedSubjectValue($env) < $this->computedStandardValue($env);
            case self::JUDGE_METHOD_LESS_THAN_OR_EQUAL:
                return $this->computedSubjectValue($env) <= $this->computedStandardValue($env);
            case self::JUDGE_METHOD_GREATER_THAN:
                return $this->computedSubjectValue($env) > $this->computedStandardValue($env);
            case self::JUDGE_METHOD_GREATER_THAN_OR_EQUAL:
                return $this->computedSubjectValue($env) >= $this->computedStandardValue($env);
            case self::JUDGE_METHOD_IS:
                return $this->computedSubjectValue($env) === $this->computedStandardValue($env);
            case self::JUDGE_METHOD_INSIDE:
                return in_array($this->computedSubjectValue($env), $this->computedStandardValue($env));
            case self::JUDGE_METHOD_OUTSIDE:
                return !in_array($this->computedSubjectValue($env), $this->computedStandardValue($env));
            case self::JUDGE_METHOD_NOT:
                return !$this->computedStandardValue($env)->computedResult($env);
            case self::JUDGE_METHOD_AND:
                foreach ($this->computedStandardValue($env) as $item) {
                    if (!$item->computedResult($env)) {
                        return false;
                    }
                }
                return true;
            case self::JUDGE_METHOD_OR:
                foreach ($this->computedStandardValue($env) as $item) {
                    if (!$item->computedResult($env)) {
                        return true;
                    }
                }
                return false;
            default:
                // if judge method not defined, use subject to compute bool
                return !!$this->computedSubjectValue($env);
        }
    }

    public function computedSubjectValue(array $env = null)
    {
        return $this->subject;
    }

    public function computedStandardValue(array $env = null)
    {
        return $this->standard;
    }

    public function equal($standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_EQUAL;
        $this->standard = $standard;
        return $this;
    }

    public function notEqual($standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_NOT_EQUAL;
        $this->standard = $standard;
        return $this;
    }

    public function lessThan($standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_LESS_THAN;
        $this->standard = $standard;
        return $this;
    }

    public function lessThanOrEqual($standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_LESS_THAN_OR_EQUAL;
        $this->standard = $standard;
        return $this;
    }

    public function greaterThan($standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_GREATER_THAN;
        $this->standard = $standard;
        return $this;
    }

    public function greaterThanOrEqual($standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_GREATER_THAN_OR_EQUAL;
        $this->standard = $standard;
        return $this;
    }

    public function is($standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_IS;
        $this->standard = $standard;
        return $this;
    }

    public function inside(array $standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_INSIDE;
        $this->standard = $standard;
        return $this;
    }

    public function outside(array $standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_OUTSIDE;
        $this->standard = $standard;
        return $this;
    }

    public function contains(string $standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_CONTAINS;
        $this->standard = $standard;
        return $this;
    }

    public function startsWith(string $standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_STARTS_WITH;
        $this->standard = $standard;
        return $this;
    }

    public function endsWith(string $standard)
    {
        $this->judgeMethod = self::JUDGE_METHOD_ENDS_WITH;
        $this->standard = $standard;
        return $this;
    }
}