<?php
/**
 * Created by solly [23.08.17 3:39]
 */

namespace insolita\rusmoney\exceptions;

use const PHP_INT_MAX;

class OverflowException extends \Exception
{
    protected $message = 'Integer value can\'t be greater than '.PHP_INT_MAX;
}
