<?php

namespace Drupal\oauthost;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Applicant users entity.
 *
 * @see \Drupal\oauthost\Entity\ApplicantUsers.
 */
class ApplicantUsersAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\oauthost\ApplicantUsersInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished applicant users entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published applicant users entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit applicant users entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete applicant users entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add applicant users entities');
  }

}
