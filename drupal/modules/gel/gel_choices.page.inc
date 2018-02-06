<?php

/**
 * @file
 * Contains gel_choices.page.inc.
 *
 * Page callback for Gel choices entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Gel choices templates.
 *
 * Default template: gel_choices.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_gel_choices(array &$variables) {
  // Fetch GelChoices Entity Object.
  $gel_choices = $variables['elements']['#gel_choices'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
