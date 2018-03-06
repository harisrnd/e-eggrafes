<?php

namespace Drupal\deploysystem;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Eggrafes config entities.
 *
 * @ingroup deploysystem
 */
class EggrafesConfigListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Eggrafes config ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\deploysystem\Entity\EggrafesConfig */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.eggrafes_config.edit_form',
      ['eggrafes_config' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
