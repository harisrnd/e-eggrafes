<?php

namespace Drupal\gel;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Gelstudenthighschool entity.
 *
 * @see \Drupal\gel\Entity\gelstudenthighschool.
 */
class gelstudenthighschoolAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\gel\Entity\gelstudenthighschoolInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished gelstudenthighschool entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published gelstudenthighschool entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit gelstudenthighschool entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete gelstudenthighschool entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add gelstudenthighschool entities');
  }

}
