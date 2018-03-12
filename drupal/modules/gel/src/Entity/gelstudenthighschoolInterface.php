<?php

namespace Drupal\gel\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Gelstudenthighschool entities.
 *
 * @ingroup gel
 */
interface gelstudenthighschoolInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Gelstudenthighschool name.
   *
   * @return string
   *   Name of the Gelstudenthighschool.
   */
  public function getName();

  /**
   * Sets the Gelstudenthighschool name.
   *
   * @param string $name
   *   The Gelstudenthighschool name.
   *
   * @return \Drupal\gel\Entity\gelstudenthighschoolInterface
   *   The called Gelstudenthighschool entity.
   */
  public function setName($name);

  /**
   * Gets the Gelstudenthighschool creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Gelstudenthighschool.
   */
  public function getCreatedTime();

  /**
   * Sets the Gelstudenthighschool creation timestamp.
   *
   * @param int $timestamp
   *   The Gelstudenthighschool creation timestamp.
   *
   * @return \Drupal\gel\Entity\gelstudenthighschoolInterface
   *   The called Gelstudenthighschool entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Gelstudenthighschool published status indicator.
   *
   * Unpublished Gelstudenthighschool are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Gelstudenthighschool is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Gelstudenthighschool.
   *
   * @param bool $published
   *   TRUE to set this Gelstudenthighschool to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\gel\Entity\gelstudenthighschoolInterface
   *   The called Gelstudenthighschool entity.
   */
  public function setPublished($published);

}
