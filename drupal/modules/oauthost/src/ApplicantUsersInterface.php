<?php

namespace Drupal\oauthost;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Applicant users entities.
 *
 * @ingroup oauthost
 */
interface ApplicantUsersInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.
  /**
   * Gets the Applicant users name.
   *
   * @return string
   *   Name of the Applicant users.
   */
  public function getName();

  /**
   * Sets the Applicant users name.
   *
   * @param string $name
   *   The Applicant users name.
   *
   * @return \Drupal\oauthost\ApplicantUsersInterface
   *   The called Applicant users entity.
   */
  public function setName($name);

  /**
   * Gets the Applicant users creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Applicant users.
   */
  public function getCreatedTime();

  /**
   * Sets the Applicant users creation timestamp.
   *
   * @param int $timestamp
   *   The Applicant users creation timestamp.
   *
   * @return \Drupal\oauthost\ApplicantUsersInterface
   *   The called Applicant users entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Applicant users published status indicator.
   *
   * Unpublished Applicant users are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Applicant users is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Applicant users.
   *
   * @param bool $published
   *   TRUE to set this Applicant users to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\oauthost\ApplicantUsersInterface
   *   The called Applicant users entity.
   */
  public function setPublished($published);

}
