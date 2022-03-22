<?php

namespace Kbrodej\Drupal\Driver\Cores;

use Drupal;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Driver\Cores\Drupal8 as OriginalCore;

/**
 * Provides core driver with more methods.
 */
class Drupal8 extends OriginalCore implements CoreInterface
{

    /**
     * @inheritDoc
     */
    public function loadContentByTitle(
        string $storageId,
        string $title,
        string $primary = 'title',
        string $sort = 'created'
    ): ?EntityInterface {
        $query = Drupal::entityTypeManager()->getStorage($storageId)->getQuery();
        $query->condition($primary, $title);
        $query->sort($sort);

        /** @var array $result */
        $result = $query->execute();
        if (count($result) < 1) {
            return null;
        }
        $entityIds = array_values($result);
        $entityId = reset($entityIds);

        if (!is_numeric($entityId)) {
            return null;
        }

        return $this->loadContentById($storageId, (int)$entityId);
    }

    /**
     * @inheritDoc
     */
    public function loadContentById(string $storageId, int $entityId): ?EntityInterface
    {
        return Drupal::entityTypeManager()->getStorage($storageId)->load($entityId);
    }
}
