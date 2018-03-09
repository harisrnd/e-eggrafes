<?php

/**
 * @file
 * Contains gel_school.page.inc.
 *
 * Page callback for Gel_school entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Gel_school templates.
 *
 * Default template: gel_school.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_gel_school(array &$variables) {
  // Fetch gel_school Entity Object.
  $gel_school = $variables['elements']['#gel_school'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
