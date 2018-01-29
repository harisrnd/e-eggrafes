<?php

/**
 * @file
 * Contains gel_admin_area.page.inc.
 *
 * Page callback for Gel admin area entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Gel admin area templates.
 *
 * Default template: gel_admin_area.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_gel_admin_area(array &$variables) {
  // Fetch GelAdminArea Entity Object.
  $gel_admin_area = $variables['elements']['#gel_admin_area'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
