<?php

namespace Drupal\deploysystem;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the School list entity.
 *
 * @see \Drupal\deploysystem\Entity\SchoolList.
 */
class SchoolListAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\deploysystem\Entity\SchoolListInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished school list entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published school list entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit school list entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete school list entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add school list entities');
  }

}
