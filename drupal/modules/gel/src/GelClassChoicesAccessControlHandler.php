<?php

namespace Drupal\gel;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Gel class choices entity.
 *
 * @see \Drupal\gel\Entity\GelClassChoices.
 */
class GelClassChoicesAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\gel\Entity\GelClassChoicesInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished gel class choices entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published gel class choices entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit gel class choices entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete gel class choices entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add gel class choices entities');
  }

}
