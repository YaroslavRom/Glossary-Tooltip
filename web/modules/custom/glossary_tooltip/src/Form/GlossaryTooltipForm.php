<?php

declare(strict_types=1);

namespace Drupal\glossary_tooltip\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a Glossary tooltip form.
 */
final class GlossaryTooltipForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'glossary_tooltip_glossary_tooltip';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['term'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Term'),
      '#required' => TRUE,
    ];

    $form['definition'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Definition'),
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Send'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    // Get the term name from the form submission.
    $term_name = $form_state->getValue('term');

    // Validate the term name using regex.
    if (!preg_match('/^[a-zA-Z\s]+$/', $term_name)) {
      $form_state->setErrorByName('term', $this->t('The term name should only contain letters and spaces.'));
    }
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // Get values from the form submission.
    $term_name = $form_state->getValue('term');
    $definition = $form_state->getValue('definition');

    // Create and save the glossary term.
    $new_term = Term::create([
      'vid' => 'glossary_tooltip', // Replace 'vocabulary' with the machine name of your vocabulary.
      'name' => $term_name,
      'description' => [
        'value' => $definition,
        'format' => 'full_html', // You can adjust the text format as needed.
      ],
    ]);

    // Ensure the term is treated as new and save it.
    $new_term->enforceIsNew();
    $new_term->save();

    // Set a success message and redirect the user.
    $this->messenger()->addStatus($this->t('The glossary term has been added successfully.'));
    $form_state->setRedirect('glossary_tooltip.glossary_tooltip');
  }

}
