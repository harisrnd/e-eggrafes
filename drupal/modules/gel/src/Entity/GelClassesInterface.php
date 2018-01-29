<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Gel classes entities.
 *
 * @ingroup gel
 */
interface GelClassesInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Gel classes name.
   *
   * @return string
   *   Name of the Gel classes.
   */
  public function getName();

  /**
   * Sets the Gel classes name.
   *
   * @param string $name
   *   The Gel classes name.
   *
   * @return \Drupal\gel\Entity\GelClassesInterface
   *   The called Gel classes entity.
   */
  public function setName($name);

  /**
   * Gets the Gel classes creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Gel classes.
   */
  public function getCreatedTime();

  /**
   * Sets the Gel classes creation timestamp.
   *
   * @param int $timestamp
   *   The Gel classes creation timestamp.
   *
   * @return \Drupal\gel\Entity\GelClassesInterface
   *   The called Gel classes entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Gel classes published status indicator.
   *
   * Unpublished Gel classes are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Gel classes is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Gel classes.
   *
   * @param bool $published
   *   TRUE to set this Gel classes to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\gel\Entity\GelClassesInterface
   *   The called Gel classes entity.
   */
  public function setPublished($published);

}
