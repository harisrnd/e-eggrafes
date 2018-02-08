<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Gel choices entities.
 *
 * @ingroup gel
 */
interface GelChoicesInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Gel choices name.
   *
   * @return string
   *   Name of the Gel choices.
   */
  public function getName();

  /**
   * Sets the Gel choices name.
   *
   * @param string $name
   *   The Gel choices name.
   *
   * @return \Drupal\gel\Entity\GelChoicesInterface
   *   The called Gel choices entity.
   */
  public function setName($name);

  /**
   * Gets the Gel choices creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Gel choices.
   */
  public function getCreatedTime();

  /**
   * Sets the Gel choices creation timestamp.
   *
   * @param int $timestamp
   *   The Gel choices creation timestamp.
   *
   * @return \Drupal\gel\Entity\GelChoicesInterface
   *   The called Gel choices entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Gel choices published status indicator.
   *
   * Unpublished Gel choices are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Gel choices is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Gel choices.
   *
   * @param bool $published
   *   TRUE to set this Gel choices to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\gel\Entity\GelChoicesInterface
   *   The called Gel choices entity.
   */
  public function setPublished($published);

}
