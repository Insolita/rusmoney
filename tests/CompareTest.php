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
        expect_that(Money::fromString('234')->equals(Money::fromInt(23400)));
        expect_that(Money::fromString('234')->equals(Money::fromPair(234, 0)));
        expect_that(Money::fromString('234')->equals(new Money(23400)));
        expect_that(Money::fromString('234')->greaterThanOrEqual(new Money(23400)));
        expect_that(Money::fromString('234')->lessThanOrEqual(new Money(23400)));
        expect_that(Money::fromString('234')->greaterThan(new Money(23399)));
        expect_that(Money::fromString('234')->lessThan(new Money(23401)));
    }
    
    public function testCompareKopecks()
    {
        expect_that(Money::fromString('0.15')->equals(Money::fromString('0.15')));
        expect_that(Money::fromString('0.15')->equals(Money::fromInt(15)));
        expect_that(Money::fromString('0.15')->equals(Money::fromPair(0, 15)));
        expect_that(Money::fromString('0.15')->equals(new Money(15)));
        expect_that(Money::fromString('0.15')->greaterThanOrEqual(new Money(15)));
        expect_that(Money::fromString('0.15')->lessThanOrEqual(new Money(15)));
        expect_that(Money::fromString('0.15')->greaterThan(new Money(5)));
        expect_that(Money::fromString('0.15')->lessThan(new Money(18)));
    }
    
    public function testCompare()
    {
        expect_that(Money::fromString('234.10')->equals(Money::fromString('234.10')));
        expect_that(Money::fromString('234.10')->equals(new Money(23410)));
        expect_that(Money::fromString('234.10')->equals(Money::fromPair(234, 10)));
        expect_that(Money::fromString('234.10')->greaterThanOrEqual(new Money(23400)));
        expect_that(Money::fromString('234.10')->lessThanOrEqual(new Money(23411)));
        expect_that(Money::fromString('234.10')->greaterThan(Money::fromPair(234, 1)));
        expect_that(Money::fromString('234.10')->lessThan(Money::fromPair(234, 20)));
    }
}
