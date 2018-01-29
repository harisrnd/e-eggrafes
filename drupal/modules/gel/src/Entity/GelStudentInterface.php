<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Gel student entities.
 *
 * @ingroup gel
 */
interface GelStudentInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Gel student name.
   *
   * @return string
   *   Name of the Gel student.
   */
  public function getName();

  /**
   * Sets the Gel student name.
   *
   * @param string $name
   *   The Gel student name.
   *
   * @return \Drupal\gel\Entity\GelStudentInterface
   *   The called Gel student entity.
   */
  public function setName($name);

  /**
   * Gets the Gel student creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Gel student.
   */
  public function getCreatedTime();

  /**
   * Sets the Gel student creation timestamp.
   *
   * @param int $timestamp
   *   The Gel student creation timestamp.
   *
   * @return \Drupal\gel\Entity\GelStudentInterface
   *   The called Gel student entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Gel student published status indicator.
   *
   * Unpublished Gel student are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Gel student is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Gel student.
   *
   * @param bool $published
   *   TRUE to set this Gel student to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\gel\Entity\GelStudentInterface
   *   The called Gel student entity.
   */
  public function setPublished($published);

}
