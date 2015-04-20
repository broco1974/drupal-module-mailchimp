<?php
/**
 * @file
 * Contains \Drupal\mailchimp_lists\Plugin\Field\FieldFormatter\MailchimpListsSubscribeDefaultFormatter.
 */

namespace Drupal\mailchimp_lists\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'mailchimp_lists_subscribe_default' formatter.
 *
 * @FieldFormatter (
 *   id = "mailchimp_lists_subscribe_default",
 *   label = @Translation("Subscription Info"),
 *   field_types = {
 *     "mailchimp_lists_subscription"
 *   }
 * )
 */
class MailchimpListsSubscribeDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $field_settings = $this->getFieldSettings();
    $settings = $this->getSettings();

    $element = array();
    $element['show_interest_groups'] = array(
      '#title' => t('Show Interest Groups'),
      '#type' => 'checkbox',
      '#description' => $settings['show_interest_groups'] ? t('Check to display interest group membership details.') : t('To display Interest Groups, first enable them in the field instance settings.'),
      '#default_value' => $settings['show_interest_groups'] && $field_settings['show_interest_groups'],
      '#disabled' => !$settings['show_interest_groups'],
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $field_settings = $this->getFieldSettings();
    $settings = $this->getSettings();

    $summary = array();

    if ($field_settings['show_interest_groups'] && $settings['show_interest_groups']) {
      $summary[] = t('Display Interest Groups');
    }
    else {
      $summary[] = t('Hide Interest Groups');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();

    /* @var $item \Drupal\mailchimp_lists\Plugin\Field\FieldType\MailchimpListsSubscription */
    foreach ($items as $delta => $item) {
      $elements[$delta] = array();

      //$elements[$delta] = array(
      //  '#type' => 'markup',
      //  '#markup' => check_plain($item->mc_list_id),
      //);
      $field_settings = $this->getFieldSettings();

      $mc_list = mailchimp_get_list($field_settings['mc_list_id']);
      $email = mailchimp_lists_load_email($item, $item->getEntity(), FALSE);

      // TODO: Display subscription form if accessible.
      //if ($display['type'] == 'mailchimp_lists_field_subscribe' && $email && entity_access('edit', $entity_type, $entity)) {
      //  $field_form_id = 'mailchimp_lists_' . $field['field_name'] . '_form';
      //  $element = drupal_get_form($field_form_id, $item, $display['settings'], $entity, $field);
      //}
      //else {
        if ($email) {
          if (mailchimp_is_subscribed($field_settings['mc_list_id'], $email)) {
            $status = t('Subscribed to %list', array('%list' => $mc_list['name']));
          }
          else {
            $status = t('Not subscribed to %list', array('%list' => $mc_list['name']));
          }
        }
        else {
          $status = t('Invalid email configuration.');
        }
        $elements[$delta]['status'] = array(
          '#markup' => $status,
          '#description' => t('@mc_list_description', array('@mc_list_description' => $item->getFieldDefinition()->getDescription())),
        );
        if ($field_settings['show_interest_groups'] && $this->getSetting('show_interest_groups')) {
          $memberinfo = mailchimp_get_memberinfo($field_settings['mc_list_id'], $email);
          if (isset($memberinfo['merges']['GROUPINGS'])) {
            $elements[$delta]['interest_groups'] = array(
              '#type' => 'fieldset',
              '#title' => t('Interest Groups'),
              '#weight' => 100,
            );
            foreach ($memberinfo['merges']['GROUPINGS'] as $grouping) {
              $items = array();
              foreach ($grouping['groups'] as $interest) {
                if ($interest['interested']) {
                  $items[] = $interest['name'];
                }
              }
              if (count($items)) {
                $elements[$delta]['interest_groups'][$grouping['id']] = array(
                  '#title' => $grouping['name'],
                  '#theme' => 'item_list',
                  '#items' => $items,
                  '#type' => 'ul',
                );
              }
            }
          }
        }
      //}
    }

    return $elements;
  }

}
