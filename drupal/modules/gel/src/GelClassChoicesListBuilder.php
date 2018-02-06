<?php

namespace Drupal\gel;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Gel class choices entities.
 *
 * @ingroup gel
 */
class GelClassChoicesListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Gel class choices ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\gel\Entity\GelClassChoices */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.gel_class_choices.edit_form',
      ['gel_class_choices' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
