<?php

namespace Drupal\gel\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Gel student choices entities.
 */
class GelStudentChoicesViewsData extends EntityViewsData {

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
