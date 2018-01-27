<?php
declare(strict_types=1);

namespace insolita\rusmoney;

use insolita\rusmoney\exceptions\OverflowException;
use insolita\rusmoney\exceptions\ParseMoneyException;
use NumberFormatter;
use const PHP_INT_MAX;
use const PHP_ROUND_HALF_UP;
use function preg_match;
use function strlen;
use function substr;

class Money implements \JsonSerializable
{
    use CompareTrait;
    use ArithmeticTrait;
    use PercentageTrait;
    
    const ISO_CURRENCY = 'RUR';
    const LOCALE = 'ru_RU';
    
    /**
     * @var int
     */
    protected $amount;
    
    /**
     * @var \NumberFormatter
     */
    private $formatter;
    
    /**
     * @param int $amount
     *
     * @throws \TypeError
     */
    public function __construct(int $amount)
    {
        $this->amount = static::checkOverflow($amount);
        $this->formatter = new NumberFormatter(self::LOCALE, NumberFormatter::CURRENCY);
    }
    
    public function isPositive(): bool
    {
        return $this->amount > 0;
    }
    
    public function isNegative(): bool
    {
        return $this->amount < 0;
    }
    
    public function isZero(): bool
    {
        return $this->amount === 0;
    }
    
    public function hasKopecks(): bool
    {
        return $this->amount % 100 !== 0;
    }
    
    public function asAbsolute(): Money
    {
        return $this->isNegative() ? $this->negate() : $this;
    }
    
    public function asNegative(): Money
    {
        return $this->isPositive() ? $this->negate() : $this;
    }
    
    /**
     * [$roubles, $kopecks] = (new Money(2340))->asPair();
     * @expect [23, 40]
     *
     * @return array
     */
    public function asPair(): array
    {
        $arr = explode('.', (string)$this->asFloat());
        $arr[0] = (int)$arr[0];
        if (count($arr) === 1) {
            $arr[] = 0;
        } else {
            if (strlen((string)$arr[1]) === 1 && (int)$arr[1] < 10) {
                $arr[1] = (int)$arr[1] . '0';
            } elseif (strlen((string)$arr[1]) > 1 && substr((string)$arr[1], 0, 1) === '0') {
                $arr[1] = (int)substr((string)$arr[1], 1, 2);
            }
        }
        return $arr;
    }
    
    public function jsonSerialize(): array
    {
        [$rubles, $kopecks] = $this->asPair();
        return [
            'amount' => $this->amount,
            'formatted' => $this->asFormattedString(),
            'rubles' => $rubles,
            'kopecks' => $kopecks,
        ];
    }
    
    public function asJson(): string
    {
        return json_encode($this->jsonSerialize());
    }
    
    public function asAmount(): int
    {
        return $this->amount;
    }
    
    public function asRoubles(): int
    {
        return (int)static::round0($this->amount / 100);
    }
    
    public function asFloat(): float
    {
        return static::round2($this->amount / 100);
    }
    
    public function asString(): string
    {
        return (string)$this->asFloat();
    }
    
    public function asFormattedString(): string
    {
        return $this->formatter->formatCurrency($this->asFloat(), self::ISO_CURRENCY);
    }
    
    public static function fromString(string $value): Money
    {
        static::checkStringByPattern($value);
        return new static((int)static::round0(100 * static::round2($value)));
    }
    
    public static function fromInt(int $amount): Money
    {
        return new static($amount);
    }
    
    public static function zero(): Money
    {
        return new static(0);
    }
    
    public static function fromPair(int $roubles, int $kopecks): Money
    {
        if ($kopecks < 0 || $kopecks > 99) {
            throw new \LogicException('Kopecks value must be in range 0..99');
        }
        return new static((int)(100 * $roubles + $kopecks));
    }
    
    public static function round2($value, $roundMode = PHP_ROUND_HALF_UP)
    {
        return round($value, 2, $roundMode);
    }
    
    public static function round0($value, $roundMode = PHP_ROUND_HALF_UP)
    {
        return round($value, 0, $roundMode);
    }
    
    protected static function checkStringByPattern(string $value): void
    {
        $maxDigits = strlen((string)PHP_INT_MAX) - 3;
        if (!preg_match('/^\-?\d{0,' . $maxDigits . '}(?:\.{0,1}\d{0,' . $maxDigits . '})$/', $value)) {
            throw new ParseMoneyException($maxDigits);
        }
    }
    
    protected static function checkOverflow($value)
    {
        if (abs($value) > PHP_INT_MAX) {
            throw new OverflowException();
        }
        return $value;
    }
}
