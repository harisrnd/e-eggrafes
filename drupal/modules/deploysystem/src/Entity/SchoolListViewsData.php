<?php

namespace Drupal\deploysystem\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for School list entities.
 */
class SchoolListViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
