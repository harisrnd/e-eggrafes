<?php

namespace Drupal\gel\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Gel admin area edit forms.
 *
 * @ingroup gel
 */
class GelAdminAreaForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\gel\Entity\GelAdminArea */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Gel admin area.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Gel admin area.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.gel_admin_area.canonical', ['gel_admin_area' => $entity->id()]);
  }

}
