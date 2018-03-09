<?php

namespace Drupal\deploysystem\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining School list entities.
 *
 * @ingroup deploysystem
 */
interface SchoolListInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the School list name.
   *
   * @return string
   *   Name of the School list.
   */
  public function getName();

  /**
   * Sets the School list name.
   *
   * @param string $name
   *   The School list name.
   *
   * @return \Drupal\deploysystem\Entity\SchoolListInterface
   *   The called School list entity.
   */
  public function setName($name);

  /**
   * Gets the School list creation timestamp.
   *
   * @return int
   *   Creation timestamp of the School list.
   */
  public function getCreatedTime();

  /**
   * Sets the School list creation timestamp.
   *
   * @param int $timestamp
   *   The School list creation timestamp.
   *
   * @return \Drupal\deploysystem\Entity\SchoolListInterface
   *   The called School list entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the School list published status indicator.
   *
   * Unpublished School list are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the School list is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a School list.
   *
   * @param bool $published
   *   TRUE to set this School list to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\deploysystem\Entity\SchoolListInterface
   *   The called School list entity.
   */
  public function setPublished($published);

}
