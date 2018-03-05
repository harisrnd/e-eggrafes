<?php

namespace Drupal\deploysystem\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Eggrafes config entities.
 *
 * @ingroup deploysystem
 */
interface EggrafesConfigInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Eggrafes config name.
   *
   * @return string
   *   Name of the Eggrafes config.
   */
  public function getName();

  /**
   * Sets the Eggrafes config name.
   *
   * @param string $name
   *   The Eggrafes config name.
   *
   * @return \Drupal\deploysystem\Entity\EggrafesConfigInterface
   *   The called Eggrafes config entity.
   */
  public function setName($name);

  /**
   * Gets the Eggrafes config creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Eggrafes config.
   */
  public function getCreatedTime();

  /**
   * Sets the Eggrafes config creation timestamp.
   *
   * @param int $timestamp
   *   The Eggrafes config creation timestamp.
   *
   * @return \Drupal\deploysystem\Entity\EggrafesConfigInterface
   *   The called Eggrafes config entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Eggrafes config published status indicator.
   *
   * Unpublished Eggrafes config are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Eggrafes config is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Eggrafes config.
   *
   * @param bool $published
   *   TRUE to set this Eggrafes config to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\deploysystem\Entity\EggrafesConfigInterface
   *   The called Eggrafes config entity.
   */
  public function setPublished($published);

}
