<?php

/**
 * @file
 * Contains school_list.page.inc.
 *
 * Page callback for School list entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for School list templates.
 *
 * Default template: school_list.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_school_list(array &$variables) {
  // Fetch SchoolList Entity Object.
  $school_list = $variables['elements']['#school_list'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
