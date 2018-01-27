<?php
/**
 * Created by solly [22.08.17 22:17]
 */

namespace insolita\rusmoney;

/**
 * Trait CompareTrait
 *
 * @mixin \insolita\rusmoney\Money
 */
trait CompareTrait
{
    public function compareTo(Money $other): int
    {
        if ($this->asAmount() == $other->asAmount()) {
            return 0;
        }
        return $this->asAmount() < $other->asAmount() ? -1 : 1;
    }
    
    public function equals(Money $other): bool
    {
        return $this->compareTo($other) == 0;
    }
    
    public function greaterThan(Money $other): bool
    {
        return $this->compareTo($other) == 1;
    }
    
    public function lessThan(Money $other): bool
    {
        return $this->compareTo($other) == -1;
    }
    
    public function greaterThanOrEqual(Money $other): bool
    {
        return $this->greaterThan($other) || $this->equals($other);
    }

    public function lessThanOrEqual(Money $other): bool
    {
        return $this->lessThan($other) || $this->equals($other);
    }
}
