<?php

namespace Drupal\gel;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Gel student entity.
 *
 * @see \Drupal\gel\Entity\GelStudent.
 */
class GelStudentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\gel\Entity\GelStudentInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished gel student entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published gel student entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit gel student entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete gel student entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add gel student entities');
  }

}
