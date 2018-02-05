<?php

/**
 * @file
 * Contains gel_student_choices.page.inc.
 *
 * Page callback for Gel student choices entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Gel student choices templates.
 *
 * Default template: gel_student_choices.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_gel_student_choices(array &$variables) {
  // Fetch GelStudentChoices Entity Object.
  $gel_student_choices = $variables['elements']['#gel_student_choices'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
