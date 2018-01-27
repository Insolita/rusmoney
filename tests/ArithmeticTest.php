<?php
/**
 * Created by solly [23.08.17 1:42]
 */

namespace tests;

use insolita\rusmoney\exceptions\OverflowException;
use insolita\rusmoney\Money;
use const PHP_INT_MAX;
use PHPUnit\Framework\TestCase;
use TypeError;
use function var_dump;

class ArithmeticTest extends TestCase
{
    public function testNegate()
    {
        expect(Money::fromString('234.19')->negate()->asAmount())->equals(-23419);
        expect(Money::fromString('-200')->negate()->asAmount())->equals(20000);
        expect((new Money(-4567))->negate()->asFloat())->equals(45.67);
        expect((new Money(5432))->negate()->asFloat())->equals(-54.32);
    }
    
    public function testMultiply()
    {
        $multi = (new Money(100))->multiply(1);
        expect($multi->asAmount())->equals(100);
        expect($multi->asFloat())->equals(1);
        
        $multi = (new Money(100))->multiply(54);
        expect($multi->asAmount())->equals(5400);
        expect($multi->asFloat())->equals(54);
        
        $multi = (new Money(100))->multiply(0.2);
        expect($multi->asAmount())->equals(20);
        expect($multi->asFloat())->equals(0.2);
        
        $multi = (new Money(100))->multiply(0.02);
        expect($multi->asAmount())->equals(2);
        expect($multi->asFloat())->equals(0.02);
        
        //!!!Warning
        $multi = (new Money(100))->multiply(0.002);
        expect($multi->asAmount())->equals(0);
        expect($multi->asFloat())->equals(0.00);
    }
    
    public function testMultiplyOverflow()
    {
        $this->expectException(OverflowException::class);
        $multi = Money::fromString('1000000000000')->multiply(500000);
        var_dump($multi);
    }
    
    public function testSumKopecks()
    {
        $t1 = Money::fromString('0.5')->add(Money::fromString('0.18'));
        expect($t1)->isInstanceOf(Money::class);
        expect($t1->asFloat())->equals(0.68);
        
        $t2 = (new Money(11))->add(new Money(9));
        expect($t2->asFloat())->equals(0.2);
        expect($t2->asAmount())->equals(20);
        
        $t2 = (new Money(99))->add(new Money(1));
        expect($t2->asFloat())->equals(1);
        expect($t2->asAmount())->equals(100);
    }
    
    public function testSumRubles()
    {
        $t1 = Money::fromString('2356')->add(Money::fromString('54321'));
        expect($t1)->isInstanceOf(Money::class);
        expect($t1->asFloat())->equals(56677);
        
        $t2 = (new Money(100))->add(new Money(200));
        expect($t2->asFloat())->equals(3);
        expect($t2->asAmount())->equals(300);
    }
    
    public function testSumMixed()
    {
        $t1 = Money::fromString('23.56')->add(Money::fromString('543.21'));
        expect($t1)->isInstanceOf(Money::class);
        expect($t1->asFloat())->equals(566.77);
        
        $t2 = (new Money(199))->add(new Money(201));
        expect($t2->asFloat())->equals(4);
        expect($t2->asAmount())->equals(400);
    }
    
    public function testSumOverflow()
    {
        $this->expectException(TypeError::class);
        (new Money(PHP_INT_MAX))->add(new Money(1));
    }
    public function testSumOverflow2()
    {
        $this->expectException(TypeError::class);
        (new Money(PHP_INT_MAX))->add(Money::fromPair(0, 1));
    }
    public function testSubtractKopecks()
    {
        $t1 = Money::fromString('0.5')->subtract(Money::fromString('0.18'));
        expect($t1)->isInstanceOf(Money::class);
        expect($t1->asFloat())->equals(0.32);
        
        $t2 = (new Money(11))->subtract(new Money(9));
        expect($t2->asFloat())->equals(0.02);
        expect($t2->asAmount())->equals(2);
        
        $t2 = (new Money(101))->subtract(new Money(1));
        expect($t2->asFloat())->equals(1);
        expect($t2->asAmount())->equals(100);
        
        $t2 = (new Money(5))->subtract(new Money(5));
        expect($t2->asFloat())->equals(0);
        expect($t2->asAmount())->equals(0);
    }
    
    public function testSubtractRubles()
    {
        $t1 = Money::fromString('1000')->subtract(Money::fromString('100'));
        expect($t1)->isInstanceOf(Money::class);
        expect($t1->asFloat())->equals(900);
        
        $t2 = (new Money(400))->subtract(new Money(200));
        expect($t2->asFloat())->equals(2);
        expect($t2->asAmount())->equals(200);
        
        $t2 = (new Money(2508907))->subtract(new Money(2508907));
        expect($t2->asFloat())->equals(0);
        expect($t2->asAmount())->equals(0);
        
        $t2 = (new Money(2508907))->subtract(new Money(2508906));
        expect($t2->asFloat())->equals(0.01);
        expect($t2->asAmount())->equals(1);
    }
    
    public function testSubtractMixed()
    {
        $t1 = Money::fromString('23.56')->subtract(Money::fromString('1.56'));
        expect($t1)->isInstanceOf(Money::class);
        expect($t1->asFloat())->equals(22);
        
        $t1 = Money::fromString('23.56')->subtract(Money::fromString('1.55'));
        expect($t1)->isInstanceOf(Money::class);
        expect($t1->asFloat())->equals(22.01);
        
        $t2 = (new Money(201))->subtract(new Money(199));
        expect($t2->asFloat())->equals(0.02);
        expect($t2->asAmount())->equals(2);
    }
    
    public function testAllocateToTargets()
    {
        $money = new Money(12000);
        $byHalf = $money->allocateToTargets(2);
        expect($byHalf)->count(2);
        expect($byHalf[0]->equals($byHalf[1]))->true();
        expect($byHalf[0]->asAmount())->equals(6000);
        
        $byThree = $money->allocateToTargets(3);
        expect($byThree)->count(3);
        expect($byThree[0]->equals($byThree[1]))->true();
        expect($byThree[0]->equals($byThree[2]))->true();
        expect($byThree[0]->asAmount())->equals(4000);
        
        $money = new Money(10000);
        $byThree = $money->allocateToTargets(3);
        expect($byThree)->count(3);
        expect($byThree[0]->greaterThan($byThree[1]))->true();
        expect($byThree[1]->equals($byThree[2]))->true();
        expect($byThree[0]->asAmount())->equals(3334);
        expect($byThree[1]->asAmount())->equals(3333);
    
        $money = new Money(4);
        $byThree = $money->allocateToTargets(3);
        expect($byThree)->count(3);
        expect($byThree[0]->asAmount())->equals(2);
        expect($byThree[1]->asAmount())->equals(1);
        expect($byThree[2]->asAmount())->equals(1);
    
        $money = new Money(2);
        $byThree = $money->allocateToTargets(3);
        expect($byThree)->count(3);
        expect($byThree[0]->asAmount())->equals(1);
        expect($byThree[1]->asAmount())->equals(1);
        expect($byThree[2]->asAmount())->equals(0);
    
        //!!!Warning
        $money = new Money(0);
        $byThree = $money->allocateToTargets(3);
        expect($byThree)->count(3);
        expect($byThree[0]->asAmount())->equals(0);
        expect($byThree[1]->asAmount())->equals(0);
        expect($byThree[2]->asAmount())->equals(0);
    }
    
    public function testAllocateByRatio()
    {
        $money = new Money(12000);
        $division = $money->allocateByRatios([20, 80]);
        expect($division[0]->asAmount())->equals(2400);
        expect($division[1]->asAmount())->equals(9600);
        
        $division = $money->allocateByRatios([50, 50]);
        expect($division[0]->asAmount())->equals(6000);
        expect($division[1]->asAmount())->equals(6000);
        
        $division = $money->allocateByRatios([30, 40, 30]);
        expect($division[0]->asAmount())->equals(3600);
        expect($division[1]->asAmount())->equals(4800);
        expect($division[2]->asAmount())->equals(3600);
        
        $division = $money->allocateByRatios([1, 1, 1]);
        expect($division[0]->asAmount())->equals(4000);
        expect($division[1]->asAmount())->equals(4000);
        expect($division[2]->asAmount())->equals(4000);
    
        $division = $money->allocateByRatios([1,2,3,4,5]);
        expect($division[0]->asAmount())->equals(800);
        expect($division[1]->asAmount())->equals(1600);
        expect($division[2]->asAmount())->equals(2400);
        expect($division[3]->asAmount())->equals(3200);
        expect($division[4]->asAmount())->equals(4000);
    
        $money = new Money(12);
        $division = $money->allocateByRatios([1, 1, 1]);
        expect($division[0]->asAmount())->equals(4);
        expect($division[1]->asAmount())->equals(4);
        expect($division[2]->asAmount())->equals(4);
    
        $money = new Money(2);
        $division = $money->allocateByRatios([1, 1, 1]);
        expect($division[0]->asAmount())->equals(1);
        expect($division[1]->asAmount())->equals(1);
        expect($division[2]->asAmount())->equals(0);
    
        $money = new Money(1);
        $division = $money->allocateByRatios([1, 1, 1]);
        expect($division[0]->asAmount())->equals(1);
        expect($division[1]->asAmount())->equals(0);
        expect($division[2]->asAmount())->equals(0);
    
        $money = new Money(1);
        $division = $money->allocateByRatios([50, 50]);
        expect($division[0]->asAmount())->equals(1);
        expect($division[1]->asAmount())->equals(0);
    }
}
