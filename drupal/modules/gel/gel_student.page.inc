<?php

/**
 * @file
 * Contains gel_student.page.inc.
 *
 * Page callback for Gel student entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Gel student templates.
 *
 * Default template: gel_student.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_gel_student(array &$variables) {
  // Fetch GelStudent Entity Object.
  $gel_student = $variables['elements']['#gel_student'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
