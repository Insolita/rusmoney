<?php

/**
 * Created by solly [23.08.17 0:40]
 */

namespace tests;

use insolita\rusmoney\exceptions\ParseMoneyException;
use insolita\rusmoney\Money;
use PHPUnit\Framework\TestCase;
use function print_r;
use TypeError;
use const PHP_INT_MAX;
use function expect;
use function expect_that;

class MoneyTest extends TestCase
{
    public function testZero()
    {
        expect(Money::zero())->equals(Money::fromAmount(0));
        expect(Money::zero()->asFloat())->equals(0);
        expect(Money::zero()->asAmount())->equals(0);
    }
    
    public function testAsAbsolute()
    {
        expect(Money::fromString('20')->asAbsolute()->asFloat())->equals(20);
        expect(Money::fromString('-20')->asAbsolute()->asFloat())->equals(20);
        expect(Money::fromString('0')->asAbsolute()->asFloat())->equals(0);
    }
    
    public function testAsNegative()
    {
        expect(Money::fromString('20')->asNegative()->asFloat())->equals(-20);
        expect(Money::fromString('-20')->asNegative()->asFloat())->equals(-20);
        expect(Money::fromString('0')->asNegative()->asFloat())->equals(0);
    }
    
    public function testAsRoubles()
    {
        expect(Money::fromString('0.01')->asRoubles())->equals(0);
        expect(Money::fromString('0.32')->asRoubles())->equals(0);
        expect(Money::fromString('10.32')->asRoubles())->equals(10);
        expect(Money::fromAmount(3440)->asRoubles())->equals(34);
        expect(Money::fromAmount(34)->asRoubles())->equals(0);
        expect(Money::fromString('10.00')->asRoubles())->equals(10);
        expect(Money::fromString('1543')->asRoubles())->equals(1543);
        expect(Money::fromString('15.43')->asRoubles())->equals(15);
        expect(Money::fromAmount(3400)->asRoubles())->equals(34);
    }
    
    public function testFromAmount()
    {
        expect(Money::fromAmount(230)->asFloat())->equals(2.30);
        expect(Money::fromAmount(-230)->asFloat())->equals(-2.30);
        expect(Money::fromAmount('-200')->asFloat())->equals(-2);
        expect(Money::fromAmount(123456789)->asFloat())->equals(1234567.89);
        expect(Money::fromAmount(4)->asFloat())->equals(0.04);
        expect(Money::fromAmount(0)->asFloat())->equals(0);
        expect(Money::fromAmount('0')->asFloat())->equals(0);
        //Warning! Suggest usage with declare(strict_types=1);
        expect(Money::fromAmount(123.34)->asAmount())->equals(123);
        expect(Money::fromAmount('123.34')->asAmount())->equals(123);
    }
    
    public function testFromIntOverflow()
    {
        $this->expectException(TypeError::class);
        Money::fromAmount('12345678901234567891234567890.5353');
    }
    
    public function testFromIntOverflow2()
    {
        $this->expectException(TypeError::class);
        Money::fromAmount(PHP_INT_MAX + 1);
    }
    
    public function testFromString()
    {
        expect(Money::fromString('2.30')->asFloat())->equals(2.30);
        expect(Money::fromString('-2.30')->asFloat())->equals(-2.30);
        expect(Money::fromString('-200')->asFloat())->equals(-200);
        expect(Money::fromString('23572.22')->asFloat())->equals(23572.22);
        expect(Money::fromString('123456789')->asFloat())->equals(123456789);
        expect(Money::fromString('-23572.22')->asFloat())->equals(-23572.22);
        expect(Money::fromString('0.04')->asFloat())->equals(0.04);
        expect(Money::fromString('0.0000004')->asFloat())->equals(0);
        expect(Money::fromString('0.12332112')->asFloat())->equals(0.12);
        expect(Money::fromString('0.29')->asFloat())->equals(0.29);
        expect(Money::fromString('0.2')->asFloat())->equals(0.20);
        expect(Money::fromString('-0.2')->asFloat())->equals(-0.20);
        expect(Money::fromString('0')->asFloat())->equals(0);
        expect(Money::fromString('-2.30')->asAmount())->equals(-230);
    }
    public function testFromNumber()
    {
        expect(Money::fromNumber(2.30)->asFloat())->equals(2.30);
        expect(Money::fromNumber(-2.30)->asFloat())->equals(-2.30);
        expect(Money::fromNumber(-200)->asFloat())->equals(-200);
        expect(Money::fromNumber(23572.22)->asFloat())->equals(23572.22);
        expect(Money::fromNumber(123456789)->asFloat())->equals(123456789);
        expect(Money::fromNumber(-23572.22)->asFloat())->equals(-23572.22);
        expect(Money::fromNumber(0.04)->asFloat())->equals(0.04);
        expect(Money::fromNumber(0.0000004)->asFloat())->equals(0);
        expect(Money::fromNumber(0.12332112)->asFloat())->equals(0.12);
        expect(Money::fromNumber(0.29)->asFloat())->equals(0.29);
        expect(Money::fromNumber(0.2)->asFloat())->equals(0.20);
        expect(Money::fromNumber(-0.2)->asFloat())->equals(-0.20);
        expect(Money::fromNumber(0)->asFloat())->equals(0);
        expect(Money::fromNumber(-2.30)->asAmount())->equals(-230);
    }
    public function testFromStringOverflow()
    {
        $this->expectException(ParseMoneyException::class);
        Money::fromString('12345678901234567891234567890.5353');
    }
    
    public function testFromStringNonPattern()
    {
        $this->expectException(ParseMoneyException::class);
        Money::fromString('123456,535355');
    }
    
    public function testFromStringNonPattern2()
    {
        $this->expectException(ParseMoneyException::class);
        Money::fromString('123 456');
    }
    
    public function testFromPair()
    {
        expect(Money::fromPair(12345, 0)->asAmount())->equals(1234500);
        expect(Money::fromPair(0, 15)->asAmount())->equals(15);
        expect(Money::fromPair(0, 1)->asAmount())->equals(1);
        expect(Money::fromPair(34, 7)->asAmount())->equals(3407);
        expect(Money::fromPair(0, 50)->asAmount())->equals(50);
        expect(Money::fromPair(234, 50)->asAmount())->equals(23450);
    }
    
    public function testFromPairOverflow()
    {
        $this->expectException(TypeError::class);
        Money::fromPair(1345678901234561234567890666, 1);
    }
    
    public function testWithoutKopecksCasts()
    {
        $m = Money::fromAmount(234900);
        expect($m->asFloat())->equals(2349);
        expect($m->asString())->equals('2349');
        expect($m->asFormattedString())->equals('2 349,00 р.');
        expect($m->asAmount())->equals(234900);
        expect($m->asPair())->equals([2349, 0]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asAmount(),
            'formatted' => $m->asFormattedString(),
            'rubles' => '2349',
            'kopecks' => '0',
        ]);
    }
    
    public function testWithoutRublesCasts()
    {
        $m = Money::fromAmount(7);
        expect($m->asFloat())->equals(0.07);
        expect($m->asString())->equals('0.07');
        expect($m->asFormattedString())->equals('0,07 р.');
        expect($m->asAmount())->equals(7);
        expect($m->asPair())->equals([0, 7]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asAmount(),
            'formatted' => $m->asFormattedString(),
            'rubles' => '0',
            'kopecks' => '7',
        ]);
    }
    
    public function testZeroCasts()
    {
        $m = Money::fromAmount(0);
        expect($m->asFloat())->equals(0);
        expect($m->asString())->equals('0');
        expect($m->asFormattedString())->equals('0,00 р.');
        expect($m->asAmount())->equals(0);
        expect($m->asPair())->equals([0, 0]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asAmount(),
            'formatted' => $m->asFormattedString(),
            'rubles' => '0',
            'kopecks' => '0',
        ]);
    }
    
    public function testOneRuble()
    {
        $m = Money::fromAmount(100);
        expect($m->asFloat())->equals(1);
        expect($m->asString())->equals('1');
        expect($m->asFormattedString())->equals('1,00 р.');
        expect($m->asAmount())->equals(100);
        expect($m->asPair())->equals([1, 0]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asAmount(),
            'formatted' => $m->asFormattedString(),
            'rubles' => '1',
            'kopecks' => '0',
        ]);
    }
    
    public function testMixedCasts()
    {
        $m = Money::fromAmount(237);
        expect($m->asFloat())->equals(2.37);
        expect($m->asString())->equals('2.37');
        expect($m->asFormattedString())->equals('2,37 р.');
        expect($m->asAmount())->equals(237);
        expect($m->asPair())->equals([2, 37]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asAmount(),
            'formatted' => $m->asFormattedString(),
            'rubles' => '2',
            'kopecks' => '37',
        ]);
    }
    
    public function testTenKopecks()
    {
        $m = Money::fromAmount(10);
        expect($m->asFloat())->equals(0.10);
        expect($m->asString())->equals('0.10');
        expect($m->asFormattedString())->equals('0,10 р.');
        expect($m->asAmount())->equals(10);
        expect($m->asPair())->equals([0, 10]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asAmount(),
            'formatted' => $m->asFormattedString(),
            'rubles' => '0',
            'kopecks' => '10',
        ]);
    }
    
    public function testPositiveNegativeZero()
    {
        expect_that(Money::fromString(0)->isZero());
        expect_that(Money::fromString(10)->isPositive());
        expect_that(Money::fromString(-10)->isNegative());
        
        expect_not(Money::fromString(0)->isPositive());
        expect_not(Money::fromString(0)->isNegative());
        
        expect_not(Money::fromString(10)->isZero());
        expect_not(Money::fromString(10)->isNegative());
        
        expect_not(Money::fromString(-10)->isZero());
        expect_not(Money::fromString(-10)->isPositive());
    }
    
    public function testHasKopecks()
    {
        expect(Money::fromString('0.32')->hasKopecks())->true();
        expect(Money::fromString('10.32')->hasKopecks())->true();
        expect(Money::fromAmount(3440)->hasKopecks())->true();
        expect(Money::fromAmount(34)->hasKopecks())->true();
        expect(Money::fromString('10.00')->hasKopecks())->false();
        expect(Money::fromString('1543')->hasKopecks())->false();
        expect(Money::fromAmount(3400)->hasKopecks())->false();
    }
}
