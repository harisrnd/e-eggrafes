<?php

namespace Drupal\oauthost\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Applicant users edit forms.
 *
 * @ingroup oauthost
 */
class ApplicantUsersForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\oauthost\Entity\ApplicantUsers */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Applicant users.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Applicant users.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.applicant_users.canonical', ['applicant_users' => $entity->id()]);
  }

}
