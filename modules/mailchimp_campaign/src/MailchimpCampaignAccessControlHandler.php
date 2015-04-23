<?php

/**
 * @file
 * Contains \Drupal\mailchimp_campaign\MailchimpCampaignAccessControlHandler.
 */

namespace Drupal\mailchimp_campaign;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access control handler for the MailchimpCampaign entity.
 */
class MailchimpCampaignAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function access(EntityInterface $entity, $operation, $langcode = LanguageInterface::LANGCODE_DEFAULT, AccountInterface $account = NULL, $return_as_object = FALSE) {

    switch ($operation) {
      case 'send':
        //return AccessResult::forbidden();
        break;
      default:
        return parent::access($entity, $operation, $langcode, $account, $return_as_object);
    }

  }

}
