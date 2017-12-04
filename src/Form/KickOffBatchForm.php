<?php

namespace Drupal\example_batch\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class KickOffBatchForm.
 */
class KickOffBatchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'kick_off_batch_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['number_to_execute'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of times to run'),
      '#required' => TRUE,
      '#min' => 1,
    ];
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Execute'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $number = $form_state->getValue('number_to_execute');
    $batch = [
      'init_message' => t('Executing a batch...'),
      'operations' => [
        [
          '_awesome_batch',
          [$number],
        ],
      ],
      'file' => drupal_get_path('module', 'example_batch') . '/example_batch.awesome_batch.inc',
    ];
    batch_set($batch);
  }
}
