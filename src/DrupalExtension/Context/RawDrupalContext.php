<?php

namespace Kbrodej\Drupal\DrupalExtension\Context;

use Drupal\Driver\Cores\CoreInterface;
use Drupal\DrupalExtension\Context\RawDrupalContext as OriginalRawDrupalContext;

/**
 * Adds more methods to the RawDrupalContext.
 */
class RawDrupalContext extends OriginalRawDrupalContext
{

    /**
     * Get current Drupal core.
     *
     * @return CoreInterface
     *   Drupal core object instance.
     */
    public function getCore(): CoreInterface
    {
        return $this->getDriver()->getCore();
    }
}
