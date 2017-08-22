<?php
/**
 * Created by solly [23.08.17 1:43]
 */

namespace tests;

use insolita\rusmoney\exceptions\OverflowException;
use insolita\rusmoney\exceptions\PrecisionExceedException;
use insolita\rusmoney\Money;
use PHPUnit\Framework\TestCase;
use TypeError;
use const PHP_INT_MAX;
use function expect;

class PercentageTest extends TestCase
{
    public function testSmallValuesPercentage()
    {
        expect((new Money(100))->calcPercent()->asFloat())
            ->equals(0.01);
        expect((new Money(220))->calcPercent()->asFloat())
            ->equals(0.02);
        expect((new Money(30))->calcPercent(10)->asFloat())
            ->equals(0.03);
    }
    
    public function testCalcPercent()
    {
        expect(Money::fromString('2.30')->calcPercent(10)->asFloat())
            ->equals(0.23);
        expect(Money::fromString('0.86')->calcPercent(10)->asFloat())
            ->equals(0.09);
        expect(Money::fromString('100')->calcPercent(18)->asFloat())
            ->equals(18);
    }
    
    public function testIncreaseOnPercent()
    {
        expect(Money::fromPair(100, 0)->increaseOnPercent(10)->asPair())
            ->equals([110, 0]);
        expect(Money::fromPair(200, 0)->increaseOnPercent(20)->asPair())
            ->equals([240, 0]);
        expect(Money::fromPair(0, 86)->increaseOnPercent(18)->asPair())
            ->equals([1, 1]);
        expect(Money::fromPair(128, 70)->increaseOnPercent(20)->asPair())
            ->equals([154, 44]);
        expect(Money::fromPair(0, 86)->increaseOnPercent(10)->asPair())
            ->equals([0, 95]);
        expect(Money::fromPair(0, 10)->increaseOnPercent(10)->asPair())
            ->equals([0, 11]);
    }
    
    public function testUnPercent()
    {
        expect(Money::fromPair(110, 0)->unPercent(10)->asPair())
            ->equals([100, 0]);
        expect(Money::fromPair(240, 0)->unPercent(20)->asPair())
            ->equals([200, 0]);
        expect(Money::fromPair(154, 44)->unPercent(20)->asPair())
            ->equals([128, 70]);
        expect(Money::fromPair(1, 1)->unPercent(18)->asPair())
            ->equals([0, 86]);
        expect(Money::fromPair(0, 95)->unPercent(10)->asPair())
            ->equals([0, 86]);
        expect(Money::fromPair(0, 11)->unPercent(10)->asPair())
            ->equals([0, 10]);
    }
    
    public function testDecreaseOnPercent()
    {
        expect(Money::fromPair(100, 0)->decreaseOnPercent(10)->asPair())
            ->equals([90, 0]);
        expect(Money::fromPair(240, 0)->decreaseOnPercent(20)->asPair())
            ->equals([192, 0]);
        expect(Money::fromPair(154, 44)->decreaseOnPercent(20)->asPair())
            ->equals([123, 55]);
    }
    
    public function testIncreaseOverflow()
    {
        $this->expectException(TypeError::class);
        Money::fromInt(PHP_INT_MAX)->increaseOnPercent(1);
    }
    
    public function testUnPercentOverflow()
    {
        $this->expectException(OverflowException::class);
        Money::fromInt(PHP_INT_MAX)->unPercent(5);
    }
    
    public function testPrecisionExceed1()
    {
        $this->expectException(PrecisionExceedException::class);
        Money::fromString('0.99')->calcPercent();
    }
    
    public function testPrecisionExceed2()
    {
        $this->expectException(PrecisionExceedException::class);
        Money::fromString('0.99')->increaseOnPercent(2);
    }
    
    public function testPrecisionExceed3()
    {
        $this->expectException(PrecisionExceedException::class);
        Money::fromString('0.99')->decreaseOnPercent(2);
    }
    
    public function testPrecisionExceed4()
    {
        $this->expectException(PrecisionExceedException::class);
        Money::fromString('0.99')->unPercent(8);
    }
}
