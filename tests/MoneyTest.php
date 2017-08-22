<?php

/**
 * Created by solly [23.08.17 0:40]
 */

namespace tests;

use insolita\rusmoney\exceptions\ParseMoneyException;
use insolita\rusmoney\Money;
use const PHP_INT_MAX;
use PHPUnit\Framework\TestCase;
use TypeError;
use function expect;
use function expect_that;

class MoneyTest extends TestCase
{
    public function testFromInt()
    {
        expect(Money::fromInt(230)->asFloat())->equals(2.30);
        expect(Money::fromInt(-230)->asFloat())->equals(-2.30);
        expect(Money::fromInt('-200')->asFloat())->equals(-2);
        expect(Money::fromInt(123456789)->asFloat())->equals(1234567.89);
        expect(Money::fromInt(4)->asFloat())->equals(0.04);
        expect(Money::fromInt(0)->asFloat())->equals(0);
        expect(Money::fromInt('0')->asFloat())->equals(0);
        //Warning! Suggest usage with declare(strict_types=1);
        expect(Money::fromInt(123.34)->asInteger())->equals(123);
        expect(Money::fromInt('123.34')->asInteger())->equals(123);
    }
    
    public function testFromIntOverflow()
    {
        $this->expectException(TypeError::class);
        Money::fromInt('12345678901234567891234567890.5353');
    }
    
    public function testFromIntOverflow2()
    {
        $this->expectException(TypeError::class);
        new Money(PHP_INT_MAX+1);
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
        expect(Money::fromString('-2.30')->asInteger())->equals(-230);
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
        expect(Money::fromPair(12345, 0)->asInteger())->equals(1234500);
        expect(Money::fromPair(0, 15)->asInteger())->equals(15);
        expect(Money::fromPair(0, 1)->asInteger())->equals(1);
        expect(Money::fromPair(34, 7)->asInteger())->equals(3407);
        expect(Money::fromPair(0, 50)->asInteger())->equals(50);
        expect(Money::fromPair(234, 50)->asInteger())->equals(23450);
    }
    
    public function testFromPairOverflow()
    {
        $this->expectException(TypeError::class);
        Money::fromPair(1345678901234561234567890666, 1);
    }
    
    public function testWithoutKopecksCasts()
    {
        $m = new Money(234900);
        expect($m->asFloat())->equals(2349);
        expect($m->asString())->equals('2349');
        expect($m->asFormattedString())->equals('2 349,00 р.');
        expect($m->asInteger())->equals(234900);
        expect($m->asPair())->equals([2349, 0]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asInteger(),
            'formatted' => $m->asFormattedString(),
            'rubles' => '2349',
            'kopecks' => '0',
        ]);
    }
    
    public function testWithoutRublesCasts()
    {
        $m = new Money(7);
        expect($m->asFloat())->equals(0.07);
        expect($m->asString())->equals('0.07');
        expect($m->asFormattedString())->equals('0,07 р.');
        expect($m->asInteger())->equals(7);
        expect($m->asPair())->equals([0, 7]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asInteger(),
            'formatted' => $m->asFormattedString(),
            'rubles' => '0',
            'kopecks' => '7',
        ]);
    }
    
    public function testZeroCasts()
    {
        $m = new Money(0);
        expect($m->asFloat())->equals(0);
        expect($m->asString())->equals('0');
        expect($m->asFormattedString())->equals('0,00 р.');
        expect($m->asInteger())->equals(0);
        expect($m->asPair())->equals([0, 0]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asInteger(),
            'formatted' => $m->asFormattedString(),
            'rubles' => '0',
            'kopecks' => '0',
        ]);
    }
    
    public function testOneRuble()
    {
        $m = new Money(100);
        expect($m->asFloat())->equals(1);
        expect($m->asString())->equals('1');
        expect($m->asFormattedString())->equals('1,00 р.');
        expect($m->asInteger())->equals(100);
        expect($m->asPair())->equals([1, 0]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asInteger(),
            'formatted' => $m->asFormattedString(),
            'rubles' => '1',
            'kopecks' => '0',
        ]);
    }
    
    public function testMixedCasts()
    {
        $m = new Money(237);
        expect($m->asFloat())->equals(2.37);
        expect($m->asString())->equals('2.37');
        expect($m->asFormattedString())->equals('2,37 р.');
        expect($m->asInteger())->equals(237);
        expect($m->asPair())->equals([2, 37]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asInteger(),
            'formatted' => $m->asFormattedString(),
            'rubles' => '2',
            'kopecks' => '37',
        ]);
    }
    
    public function testTenKopecks()
    {
        $m = new Money(10);
        expect($m->asFloat())->equals(0.10);
        expect($m->asString())->equals('0.10');
        expect($m->asFormattedString())->equals('0,10 р.');
        expect($m->asInteger())->equals(10);
        expect($m->asPair())->equals([0, 10]);
        expect($m->jsonSerialize())->equals([
            'amount' => $m->asInteger(),
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
}
