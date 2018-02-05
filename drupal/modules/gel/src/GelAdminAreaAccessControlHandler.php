<?php

namespace Drupal\gel;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Gel admin area entity.
 *
 * @see \Drupal\gel\Entity\GelAdminArea.
 */
class GelAdminAreaAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\gel\Entity\GelAdminAreaInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished gel admin area entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published gel admin area entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit gel admin area entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete gel admin area entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add gel admin area entities');
  }

}
