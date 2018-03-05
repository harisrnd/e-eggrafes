<?php

namespace Drupal\deploysystem\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Eggrafes config edit forms.
 *
 * @ingroup deploysystem
 */
class EggrafesConfigForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\deploysystem\Entity\EggrafesConfig */
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
        drupal_set_message($this->t('Created the %label Eggrafes config.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Eggrafes config.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.eggrafes_config.canonical', ['eggrafes_config' => $entity->id()]);
  }

}
