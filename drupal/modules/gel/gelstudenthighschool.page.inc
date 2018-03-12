<?php

/**
 * @file
 * Contains gelstudenthighschool.page.inc.
 *
 * Page callback for Gelstudenthighschool entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Gelstudenthighschool templates.
 *
 * Default template: gelstudenthighschool.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_gelstudenthighschool(array &$variables) {
  // Fetch gelstudenthighschool Entity Object.
  $gelstudenthighschool = $variables['elements']['#gelstudenthighschool'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
