<?php
/**
 * Created by solly [23.08.17 1:43]
 */

namespace tests;

use insolita\rusmoney\Money;
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
{
    public function testCompareRubles()
    {
        expect_that(Money::fromString('234')->equals(Money::fromString('234.00')));
        expect_that(Money::fromString('234')->equals(Money::fromAmount(23400)));
        expect_that(Money::fromString('234')->equals(Money::fromPair(234, 0)));
        expect_that(Money::fromString('234')->equals(Money::fromAmount(23400)));
        expect_that(Money::fromString('234')->greaterThanOrEqual(Money::fromAmount(23400)));
        expect_that(Money::fromString('234')->lessThanOrEqual(Money::fromAmount(23400)));
        expect_that(Money::fromString('234')->greaterThan(Money::fromAmount(23399)));
        expect_that(Money::fromString('234')->lessThan(Money::fromAmount(23401)));
    }
    
    public function testCompareKopecks()
    {
        expect_that(Money::fromString('0.15')->equals(Money::fromString('0.15')));
        expect_that(Money::fromString('0.15')->equals(Money::fromAmount(15)));
        expect_that(Money::fromString('0.15')->equals(Money::fromPair(0, 15)));
        expect_that(Money::fromString('0.15')->equals(Money::fromAmount(15)));
        expect_that(Money::fromString('0.15')->greaterThanOrEqual(Money::fromAmount(15)));
        expect_that(Money::fromString('0.15')->lessThanOrEqual(Money::fromAmount(15)));
        expect_that(Money::fromString('0.15')->greaterThan(Money::fromAmount(5)));
        expect_that(Money::fromString('0.15')->lessThan(Money::fromAmount(18)));
    }
    
    public function testCompare()
    {
        expect_that(Money::fromString('234.10')->equals(Money::fromString('234.10')));
        expect_that(Money::fromString('234.10')->equals(Money::fromAmount(23410)));
        expect_that(Money::fromString('234.10')->equals(Money::fromPair(234, 10)));
        expect_that(Money::fromString('234.10')->greaterThanOrEqual(Money::fromAmount(23400)));
        expect_that(Money::fromString('234.10')->lessThanOrEqual(Money::fromAmount(23411)));
        expect_that(Money::fromString('234.10')->greaterThan(Money::fromPair(234, 1)));
        expect_that(Money::fromString('234.10')->lessThan(Money::fromPair(234, 20)));
    }
}
