<?php

namespace Drupal\gel;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Gel student choices entities.
 *
 * @ingroup gel
 */
class GelStudentChoicesListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Gel student choices ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\gel\Entity\GelStudentChoices */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.gel_student_choices.edit_form',
      ['gel_student_choices' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
