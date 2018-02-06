<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Gel class choices entities.
 *
 * @ingroup gel
 */
interface GelClassChoicesInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Gel class choices name.
   *
   * @return string
   *   Name of the Gel class choices.
   */
  public function getName();

  /**
   * Sets the Gel class choices name.
   *
   * @param string $name
   *   The Gel class choices name.
   *
   * @return \Drupal\gel\Entity\GelClassChoicesInterface
   *   The called Gel class choices entity.
   */
  public function setName($name);

  /**
   * Gets the Gel class choices creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Gel class choices.
   */
  public function getCreatedTime();

  /**
   * Sets the Gel class choices creation timestamp.
   *
   * @param int $timestamp
   *   The Gel class choices creation timestamp.
   *
   * @return \Drupal\gel\Entity\GelClassChoicesInterface
   *   The called Gel class choices entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Gel class choices published status indicator.
   *
   * Unpublished Gel class choices are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Gel class choices is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Gel class choices.
   *
   * @param bool $published
   *   TRUE to set this Gel class choices to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\gel\Entity\GelClassChoicesInterface
   *   The called Gel class choices entity.
   */
  public function setPublished($published);

}
