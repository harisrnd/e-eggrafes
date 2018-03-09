<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Gel_school entities.
 *
 * @ingroup gel
 */
interface gel_schoolInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Gel_school name.
   *
   * @return string
   *   Name of the Gel_school.
   */
  public function getName();

  /**
   * Sets the Gel_school name.
   *
   * @param string $name
   *   The Gel_school name.
   *
   * @return \Drupal\gel\Entity\gel_schoolInterface
   *   The called Gel_school entity.
   */
  public function setName($name);

  /**
   * Gets the Gel_school creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Gel_school.
   */
  public function getCreatedTime();

  /**
   * Sets the Gel_school creation timestamp.
   *
   * @param int $timestamp
   *   The Gel_school creation timestamp.
   *
   * @return \Drupal\gel\Entity\gel_schoolInterface
   *   The called Gel_school entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Gel_school published status indicator.
   *
   * Unpublished Gel_school are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Gel_school is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Gel_school.
   *
   * @param bool $published
   *   TRUE to set this Gel_school to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\gel\Entity\gel_schoolInterface
   *   The called Gel_school entity.
   */
  public function setPublished($published);

}
