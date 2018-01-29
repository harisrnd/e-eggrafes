<?php

namespace Drupal\epal;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of EPAL Student entities.
 *
 * @ingroup epal
 */
class EpalStudentListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
	$header['epaluser_id'] = $this->t('ID χρήστη ΕΠΑΛ');
    $header['name'] = $this->t('Όνομα');
    $header['studentsurname'] = $this->t('Επώνυμο');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\epal\Entity\EpalStudent */
    $row['id'] = $entity->id();

	$row['epaluser_id'] = $this->l(
      $entity->getEpaluser_id(),
      new Url(
        'entity.epal_student.edit_form', array(
          'epal_student' => $entity->id(),
        )
      )
    );

    $row['name'] = $this->l(
      $entity->getName(),
      new Url(
        'entity.epal_student.edit_form', array(
          'epal_student' => $entity->id(),
        )
      )
    );

    $row['studentsurname'] = $this->l(
      $entity->getStudentSurname(),
      new Url(
        'entity.epal_student.edit_form', array(
          'epal_student' => $entity->id(),
        )
      )
    );

    return $row + parent::buildRow($entity);
  }

}
