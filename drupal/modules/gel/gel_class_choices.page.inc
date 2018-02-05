<?php

/**
 * @file
 * Contains gel_class_choices.page.inc.
 *
 * Page callback for Gel class choices entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Gel class choices templates.
 *
 * Default template: gel_class_choices.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_gel_class_choices(array &$variables) {
  // Fetch GelClassChoices Entity Object.
  $gel_class_choices = $variables['elements']['#gel_class_choices'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
