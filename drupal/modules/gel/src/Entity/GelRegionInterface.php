<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Gel region entities.
 *
 * @ingroup gel
 */
interface GelRegionInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Gel region name.
   *
   * @return string
   *   Name of the Gel region.
   */
  public function getName();

  /**
   * Sets the Gel region name.
   *
   * @param string $name
   *   The Gel region name.
   *
   * @return \Drupal\gel\Entity\GelRegionInterface
   *   The called Gel region entity.
   */
  public function setName($name);

  /**
   * Gets the Gel region creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Gel region.
   */
  public function getCreatedTime();

  /**
   * Sets the Gel region creation timestamp.
   *
   * @param int $timestamp
   *   The Gel region creation timestamp.
   *
   * @return \Drupal\gel\Entity\GelRegionInterface
   *   The called Gel region entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Gel region published status indicator.
   *
   * Unpublished Gel region are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Gel region is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Gel region.
   *
   * @param bool $published
   *   TRUE to set this Gel region to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\gel\Entity\GelRegionInterface
   *   The called Gel region entity.
   */
  public function setPublished($published);

}
