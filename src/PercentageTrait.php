<?php
/**
 * Created by solly [22.08.17 22:30]
 */

namespace insolita\rusmoney;

use insolita\rusmoney\exceptions\PrecisionExceedException;
use const PHP_ROUND_HALF_UP;

/**
 * @mixin \insolita\rusmoney\Money
 */
trait PercentageTrait
{
    public function increaseOnPercent(int $percent, $roundMode = PHP_ROUND_HALF_UP): Money
    {
        $this->checkPrecisionSupport($percent);
        return $this->add($this->calcPercent($percent, $roundMode));
    }
    
    public function decreaseOnPercent(int $percent, $roundMode = PHP_ROUND_HALF_UP): Money
    {
        $this->checkPrecisionSupport($percent);
        return $this->subtract($this->calcPercent($percent, $roundMode));
    }
    
    public function unPercent(int $percent, $roundMode = PHP_ROUND_HALF_UP): Money
    {
        $this->checkPrecisionSupport($percent);
        $unPercent = static::checkOverflow(100 * $this->asAmount()) / (100 + $percent);
        return new static((int)static::round0($unPercent, $roundMode));
    }
    
    public function calcPercent($percent = 1, $roundMode = PHP_ROUND_HALF_UP): Money
    {
        $this->checkPrecisionSupport($percent);
        $perc = static::checkOverflow((0.01 * $percent) * $this->asAmount());
        return new static(intval(static::round0($perc, $roundMode)));
    }
    
    protected function checkPrecisionSupport($percent)
    {
        if ($this->asAmount() < 100 && $percent < 10) {
            throw new PrecisionExceedException();
        }
    }
}
