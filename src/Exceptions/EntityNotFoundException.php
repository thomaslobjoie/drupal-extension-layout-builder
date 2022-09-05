<?php

namespace Kbrodej\Drupal\Exceptions;

use Behat\Testwork\Tester\Exception\TesterException;
use RuntimeException;

/**
 * EntityNotFound exception.
 */
class EntityNotFoundException extends RuntimeException implements TesterException
{
}
