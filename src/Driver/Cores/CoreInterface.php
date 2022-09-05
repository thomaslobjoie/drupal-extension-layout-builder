<?php

namespace Kbrodej\Drupal\Driver\Cores;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityInterface;
use NuvoleWeb\Drupal\Driver\Cores\CoreInterface as NuvoleWebCoreInterface;

/**
 * Provides core extendability.
 */
interface CoreInterface extends NuvoleWebCoreInterface
{
    /**
     * Loads content by type and title.
     *
     * @param string $storageId
     * @param string $title
     * @param string $primary
     * @param string $sort
     * @return EntityInterface|null
     *
     * @throws InvalidPluginDefinitionException
     * @throws PluginNotFoundException
     *
     */
    public function loadContentByTitle(
        string $storageId,
        string $title,
        string $primary = 'title',
        string $sort = 'created'
    ): ?EntityInterface;

    /**
     * Loads content by id.
     *
     * @param string $storageId
     * @param int $entityId
     * @return EntityInterface|null
     *
     * @throws InvalidPluginDefinitionException
     * @throws PluginNotFoundException
     */
    public function loadContentById(string $storageId, int $entityId): ?EntityInterface;
}
