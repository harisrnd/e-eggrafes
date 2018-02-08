<?php

namespace Drupal\gel;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Gel region entity.
 *
 * @see \Drupal\gel\Entity\GelRegion.
 */
class GelRegionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\gel\Entity\GelRegionInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished gel region entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published gel region entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit gel region entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete gel region entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add gel region entities');
  }

}
