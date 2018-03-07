<?php

/**
 * @file
 * Contains eggrafes_config.page.inc.
 *
 * Page callback for Eggrafes config entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Eggrafes config templates.
 *
 * Default template: eggrafes_config.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_eggrafes_config(array &$variables) {
  // Fetch EggrafesConfig Entity Object.
  $eggrafes_config = $variables['elements']['#eggrafes_config'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
