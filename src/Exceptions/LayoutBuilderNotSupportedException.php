<?php

namespace Kbrodej\Drupal\DrupalExtension\Exceptions;

use Behat\Testwork\Tester\Exception\TesterException;
use RuntimeException;

class LayoutBuilderNotSupportedException extends RuntimeException implements TesterException
{

}
