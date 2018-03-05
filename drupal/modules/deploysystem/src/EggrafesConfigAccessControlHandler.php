<?php

namespace Drupal\deploysystem;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Eggrafes config entity.
 *
 * @see \Drupal\deploysystem\Entity\EggrafesConfig.
 */
class EggrafesConfigAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\deploysystem\Entity\EggrafesConfigInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished eggrafes config entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published eggrafes config entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit eggrafes config entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete eggrafes config entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add eggrafes config entities');
  }

}
