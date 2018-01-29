<?php

namespace Drupal\gel\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Gel choices entities.
 */
class GelChoicesViewsData extends EntityViewsData {

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
