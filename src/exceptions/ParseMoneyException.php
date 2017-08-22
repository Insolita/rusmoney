<?php
/**
 * Created by solly [23.08.17 2:08]
 */

namespace insolita\rusmoney\exceptions;

class ParseMoneyException extends \Exception
{
    public function __construct($maxDigits, $code = 0, \Throwable $previous = null)
    {
        $message = 'String must contains only digits and dot as decimal separator;'
            .' digits length cant be greater than '.$maxDigits;
        parent::__construct($message, $code, $previous);
    }
}
