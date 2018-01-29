<?php

namespace Drupal\casost\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for CASOST Session edit forms.
 *
 * @ingroup casost
 */
class CASOSTSessionForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\casost\Entity\CASOSTSession */
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
        drupal_set_message($this->t('Created the %label CASOST Session.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label CASOST Session.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.casost_session.canonical', ['casost_session' => $entity->id()]);
  }

}
