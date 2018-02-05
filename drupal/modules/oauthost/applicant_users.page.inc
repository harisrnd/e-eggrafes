<?php

/**
 * @file
 * Contains applicant_users.page.inc.
 *
 * Page callback for Applicant users entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Applicant users templates.
 *
 * Default template: applicant_users.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_applicant_users(array &$variables) {
  // Fetch ApplicantUsers Entity Object.
  $applicant_users = $variables['elements']['#applicant_users'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
