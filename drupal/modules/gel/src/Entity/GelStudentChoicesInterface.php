<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Gel student choices entities.
 *
 * @ingroup gel
 */
interface GelStudentChoicesInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Gel student choices name.
   *
   * @return string
   *   Name of the Gel student choices.
   */
  public function getName();

  /**
   * Sets the Gel student choices name.
   *
   * @param string $name
   *   The Gel student choices name.
   *
   * @return \Drupal\gel\Entity\GelStudentChoicesInterface
   *   The called Gel student choices entity.
   */
  public function setName($name);

  /**
   * Gets the Gel student choices creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Gel student choices.
   */
  public function getCreatedTime();

  /**
   * Sets the Gel student choices creation timestamp.
   *
   * @param int $timestamp
   *   The Gel student choices creation timestamp.
   *
   * @return \Drupal\gel\Entity\GelStudentChoicesInterface
   *   The called Gel student choices entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Gel student choices published status indicator.
   *
   * Unpublished Gel student choices are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Gel student choices is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Gel student choices.
   *
   * @param bool $published
   *   TRUE to set this Gel student choices to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\gel\Entity\GelStudentChoicesInterface
   *   The called Gel student choices entity.
   */
  public function setPublished($published);

}
