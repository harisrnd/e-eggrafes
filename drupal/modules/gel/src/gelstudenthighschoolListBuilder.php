<?php

namespace Drupal\gel;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Gelstudenthighschool entities.
 *
 * @ingroup gel
 */
class gelstudenthighschoolListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Gelstudenthighschool ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\gel\Entity\gelstudenthighschool */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.gelstudenthighschool.edit_form',
      ['gelstudenthighschool' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
