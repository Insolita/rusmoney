<?php
declare(strict_types=1);

namespace insolita\rusmoney;

/**
 * @mixin \insolita\rusmoney\Money
 */
trait ArithmeticTrait
{
    public function add(Money $money): Money
    {
        return new static($this->asAmount() + $money->asAmount());
    }
    
    public function subtract(Money $money): Money
    {
        return new static($this->asAmount() - $money->asAmount());
    }
    
    public function multiply($factor, $roundMode = PHP_ROUND_HALF_UP): Money
    {
        $mul = static::checkOverflow($this->asAmount() * static::checkOverflow($factor));
        return new static((int)static::round0($mul, $roundMode));
    }
    
    /**
     * alias for allocateToTargets
     *
     * @param int $number
     *
     * @return array|\insolita\rusmoney\Money[]
     */
    public function divide(int $number): array
    {
        return $this->allocateToTargets($number);
    }
    
    /**
     * Allocate the monetary value represented by this Money object
     * among N targets.
     *
     * @example
     * $result1 = (new Money(12000))->allocateToTargets(2);
     * $result2 = (new Money(12000))->allocateByRatios(3);
     * $result3 = (new Money(10000))->allocateByRatios(3);
     * @expect
     * $result1 = [Money(6000), Money(6000)];
     * $result2 = [Money(4000), Money(4000), Money(4000)];
     * $result3 = [Money(3334), Money(3333), Money(3333)];
     *
     * @param int $number
     *
     * @return static[]
     */
    public function allocateToTargets(int $number): array
    {
        $low = new static(intval($this->asAmount() / $number));
        $high = new static($low->asAmount() + 1);
        $remainder = $this->asAmount() % $number;
        $result = [];
        
        for ($i = 0; $i < $remainder; $i++) {
            $result[] = $high;
        }
        
        for ($i = $remainder; $i < $number; $i++) {
            $result[] = $low;
        }
        
        return $result;
    }
    
    /**
     * Allocate the monetary value represented by this Money object
     * using a list of ratios.
     *
     * @example
     * $result1 = (new Money(12000))->allocateByRatios([80, 20]);
     * $result2 = (new Money(12000))->allocateByRatios([50, 50]);
     * $result3 = (new Money(12000))->allocateByRatios([30, 30, 40]);
     * @expect
     * $result1 = [Money(9600), Money(2400)];
     * $result2 = [Money(6000), Money(6000)];
     * $result3 = [Money(3600), Money(3600), Money(4800)];
     *
     * @param  array|int[] $ratios
     *
     * @return static[]
     */
    public function allocateByRatios(array $ratios): array
    {
        $result = [];
        $total = array_sum($ratios);
        $remainder = $this->asAmount();
        
        for ($i = 0; $i < count($ratios); $i++) {
            $amount = intval($this->asAmount() * $ratios[$i] / $total);
            $result[] = new static($amount);
            $remainder -= $amount;
        }
        
        for ($i = 0; $i < $remainder; $i++) {
            $result[$i] = new static($result[$i]->asAmount() + 1);
        }
        return $result;
    }
    
    public function negate()
    {
        return new static(-1 * $this->asAmount());
    }
    
}
