<?php

/**
 * @file
 * Contains gel_region.page.inc.
 *
 * Page callback for Gel region entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Gel region templates.
 *
 * Default template: gel_region.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_gel_region(array &$variables) {
  // Fetch GelRegion Entity Object.
  $gel_region = $variables['elements']['#gel_region'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
