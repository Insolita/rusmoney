<?php
/**
 * Created by solly [23.08.17 1:42]
 */

namespace tests;

use insolita\rusmoney\exceptions\OverflowException;
use insolita\rusmoney\Money;
use PHPUnit\Framework\TestCase;
use TypeError;
use function expect;
use function var_dump;
use const PHP_INT_MAX;

class ArithmeticTest extends TestCase
{
    public function testNegate()
    {
        expect(Money::fromString('234.19')->negate()->asAmount())->equals(-23419);
        expect(Money::fromString('-200')->negate()->asAmount())->equals(20000);
        expect((Money::fromAmount(-4567))->negate()->asFloat())->equals(45.67);
        expect((Money::fromAmount(5432))->negate()->asFloat())->equals(-54.32);
    }
    
    public function testMultiply()
    {
        $multi = (Money::fromAmount(100))->multiply(1);
        expect($multi->asAmount())->equals(100);
        expect($multi->asFloat())->equals(1);
        
        $multi = (Money::fromAmount(100))->multiply(54);
        expect($multi->asAmount())->equals(5400);
        expect($multi->asFloat())->equals(54);
        
        $multi = (Money::fromAmount(100))->multiply(0.2);
        expect($multi->asAmount())->equals(20);
        expect($multi->asFloat())->equals(0.2);
        
        $multi = (Money::fromAmount(100))->multiply(0.02);
        expect($multi->asAmount())->equals(2);
        expect($multi->asFloat())->equals(0.02);
        
        //!!!Warning
        $multi = (Money::fromAmount(100))->multiply(0.002);
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
        
        $t2 = (Money::fromAmount(11))->add(Money::fromAmount(9));
        expect($t2->asFloat())->equals(0.2);
        expect($t2->asAmount())->equals(20);
        
        $t2 = (Money::fromAmount(99))->add(Money::fromAmount(1));
        expect($t2->asFloat())->equals(1);
        expect($t2->asAmount())->equals(100);
    }
    
    public function testSumRubles()
    {
        $t1 = Money::fromString('2356')->add(Money::fromString('54321'));
        expect($t1)->isInstanceOf(Money::class);
        expect($t1->asFloat())->equals(56677);
        
        $t2 = (Money::fromAmount(100))->add(Money::fromAmount(200));
        expect($t2->asFloat())->equals(3);
        expect($t2->asAmount())->equals(300);
    }
    
    public function testSumMixed()
    {
        $t1 = Money::fromString('23.56')->add(Money::fromString('543.21'));
        expect($t1)->isInstanceOf(Money::class);
        expect($t1->asFloat())->equals(566.77);
        
        $t2 = (Money::fromAmount(199))->add(Money::fromAmount(201));
        expect($t2->asFloat())->equals(4);
        expect($t2->asAmount())->equals(400);
    }
    
    public function testSumOverflow()
    {
        $this->expectException(TypeError::class);
        (Money::fromAmount(PHP_INT_MAX))->add(Money::fromAmount(1));
    }
    
    public function testSumOverflow2()
    {
        $this->expectException(TypeError::class);
        (Money::fromAmount(PHP_INT_MAX))->add(Money::fromPair(0, 1));
    }
    
    public function testSubtractKopecks()
    {
        $t1 = Money::fromString('0.5')->subtract(Money::fromString('0.18'));
        expect($t1)->isInstanceOf(Money::class);
        expect($t1->asFloat())->equals(0.32);
        
        $t2 = (Money::fromAmount(11))->subtract(Money::fromAmount(9));
        expect($t2->asFloat())->equals(0.02);
        expect($t2->asAmount())->equals(2);
        
        $t2 = (Money::fromAmount(101))->subtract(Money::fromAmount(1));
        expect($t2->asFloat())->equals(1);
        expect($t2->asAmount())->equals(100);
        
        $t2 = (Money::fromAmount(5))->subtract(Money::fromAmount(5));
        expect($t2->asFloat())->equals(0);
        expect($t2->asAmount())->equals(0);
    }
    
    public function testSubtractRubles()
    {
        $t1 = Money::fromString('1000')->subtract(Money::fromString('100'));
        expect($t1)->isInstanceOf(Money::class);
        expect($t1->asFloat())->equals(900);
        
        $t2 = (Money::fromAmount(400))->subtract(Money::fromAmount(200));
        expect($t2->asFloat())->equals(2);
        expect($t2->asAmount())->equals(200);
        
        $t2 = (Money::fromAmount(2508907))->subtract(Money::fromAmount(2508907));
        expect($t2->asFloat())->equals(0);
        expect($t2->asAmount())->equals(0);
        
        $t2 = (Money::fromAmount(2508907))->subtract(Money::fromAmount(2508906));
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
        
        $t2 = (Money::fromAmount(201))->subtract(Money::fromAmount(199));
        expect($t2->asFloat())->equals(0.02);
        expect($t2->asAmount())->equals(2);
    }
    
    public function testAllocateToTargets()
    {
        $money = Money::fromAmount(12000);
        $byHalf = $money->allocateToTargets(2);
        expect($byHalf)->count(2);
        expect($byHalf[0]->equals($byHalf[1]))->true();
        expect($byHalf[0]->asAmount())->equals(6000);
        
        $byThree = $money->allocateToTargets(3);
        expect($byThree)->count(3);
        expect($byThree[0]->equals($byThree[1]))->true();
        expect($byThree[0]->equals($byThree[2]))->true();
        expect($byThree[0]->asAmount())->equals(4000);
        
        $money = Money::fromAmount(10000);
        $byThree = $money->allocateToTargets(3);
        expect($byThree)->count(3);
        expect($byThree[0]->greaterThan($byThree[1]))->true();
        expect($byThree[1]->equals($byThree[2]))->true();
        expect($byThree[0]->asAmount())->equals(3334);
        expect($byThree[1]->asAmount())->equals(3333);
        
        $money = Money::fromAmount(4);
        $byThree = $money->allocateToTargets(3);
        expect($byThree)->count(3);
        expect($byThree[0]->asAmount())->equals(2);
        expect($byThree[1]->asAmount())->equals(1);
        expect($byThree[2]->asAmount())->equals(1);
        
        $money = Money::fromAmount(2);
        $byThree = $money->allocateToTargets(3);
        expect($byThree)->count(3);
        expect($byThree[0]->asAmount())->equals(1);
        expect($byThree[1]->asAmount())->equals(1);
        expect($byThree[2]->asAmount())->equals(0);
        
        //!!!Warning
        $money = Money::fromAmount(0);
        $byThree = $money->allocateToTargets(3);
        expect($byThree)->count(3);
        expect($byThree[0]->asAmount())->equals(0);
        expect($byThree[1]->asAmount())->equals(0);
        expect($byThree[2]->asAmount())->equals(0);
    }
    
    public function testDivideDirty()
    {
        $money = Money::fromAmount(3000);
        expect($money->divideDirty(3)->asAmount())->equals(1000);
    
        $money = Money::fromAmount(3333);
        expect($money->divideDirty(3)->asAmount())->equals(1111);
    
        $money = Money::fromAmount(30);
        expect($money->divideDirty(3)->asAmount())->equals(10);
        
        $money = Money::fromAmount(30);
        expect($money->divideDirty(25)->asAmount())->equals(1);
        
        $money = Money::fromAmount(3);
        expect($money->divideDirty(3)->asAmount())->equals(1);
    
        $money = Money::fromAmount(3);
        expect($money->divideDirty(10)->asAmount())->equals(0);
    
        $money = Money::fromAmount(3);
        expect($money->divideDirty(0.1)->asAmount())->equals(30);
    
        $money = Money::fromAmount(3);
        expect($money->divideDirty(1)->asAmount())->equals(3);
    
        $money = Money::fromAmount(-30);
        expect($money->divideDirty(2)->asAmount())->equals(-15);
    
        $money = Money::fromAmount(30);
        expect($money->divideDirty(-2)->asAmount())->equals(-15);
    
        $money = Money::fromAmount(-30);
        expect($money->divideDirty(-2)->asAmount())->equals(15);
    }
    
    public function testAllocateByRatio()
    {
        $money = Money::fromAmount(12000);
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
        
        $division = $money->allocateByRatios([1, 2, 3, 4, 5]);
        expect($division[0]->asAmount())->equals(800);
        expect($division[1]->asAmount())->equals(1600);
        expect($division[2]->asAmount())->equals(2400);
        expect($division[3]->asAmount())->equals(3200);
        expect($division[4]->asAmount())->equals(4000);
        
        $money = Money::fromAmount(12);
        $division = $money->allocateByRatios([1, 1, 1]);
        expect($division[0]->asAmount())->equals(4);
        expect($division[1]->asAmount())->equals(4);
        expect($division[2]->asAmount())->equals(4);
        
        $money = Money::fromAmount(2);
        $division = $money->allocateByRatios([1, 1, 1]);
        expect($division[0]->asAmount())->equals(1);
        expect($division[1]->asAmount())->equals(1);
        expect($division[2]->asAmount())->equals(0);
        
        $money = Money::fromAmount(1);
        $division = $money->allocateByRatios([1, 1, 1]);
        expect($division[0]->asAmount())->equals(1);
        expect($division[1]->asAmount())->equals(0);
        expect($division[2]->asAmount())->equals(0);
        
        $money = Money::fromAmount(1);
        $division = $money->allocateByRatios([50, 50]);
        expect($division[0]->asAmount())->equals(1);
        expect($division[1]->asAmount())->equals(0);
    
        $money = Money::fromAmount(10000);
        $division = $money->allocateByRatios([50,30, 10, 9, 0.5, 0.4, 0.09, 0.01]);
        expect($division[0]->asAmount())->equals(5000);
        expect($division[1]->asAmount())->equals(3000);
        expect($division[2]->asAmount())->equals(1000);
        expect($division[3]->asAmount())->equals(900);
        expect($division[4]->asAmount())->equals(50);
        expect($division[5]->asAmount())->equals(40);
        expect($division[6]->asAmount())->equals(9);
        expect($division[7]->asAmount())->equals(1);
    }
}
