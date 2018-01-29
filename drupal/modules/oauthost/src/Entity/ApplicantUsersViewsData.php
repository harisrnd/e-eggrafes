<?php

namespace Drupal\oauthost\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Applicant users entities.
 */
class ApplicantUsersViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['applicant_users']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Applicant users'),
      'help' => $this->t('The Applicant users ID.'),
    );

    return $data;
  }

}
