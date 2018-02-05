<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Gel admin area entities.
 *
 * @ingroup gel
 */
interface GelAdminAreaInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Gel admin area name.
   *
   * @return string
   *   Name of the Gel admin area.
   */
  public function getName();

  /**
   * Sets the Gel admin area name.
   *
   * @param string $name
   *   The Gel admin area name.
   *
   * @return \Drupal\gel\Entity\GelAdminAreaInterface
   *   The called Gel admin area entity.
   */
  public function setName($name);

  /**
   * Gets the Gel admin area creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Gel admin area.
   */
  public function getCreatedTime();

  /**
   * Sets the Gel admin area creation timestamp.
   *
   * @param int $timestamp
   *   The Gel admin area creation timestamp.
   *
   * @return \Drupal\gel\Entity\GelAdminAreaInterface
   *   The called Gel admin area entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Gel admin area published status indicator.
   *
   * Unpublished Gel admin area are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Gel admin area is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Gel admin area.
   *
   * @param bool $published
   *   TRUE to set this Gel admin area to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\gel\Entity\GelAdminAreaInterface
   *   The called Gel admin area entity.
   */
  public function setPublished($published);

}
