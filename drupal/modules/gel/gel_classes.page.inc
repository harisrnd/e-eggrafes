<?php

/**
 * @file
 * Contains gel_classes.page.inc.
 *
 * Page callback for Gel classes entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Gel classes templates.
 *
 * Default template: gel_classes.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_gel_classes(array &$variables) {
  // Fetch GelClasses Entity Object.
  $gel_classes = $variables['elements']['#gel_classes'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
