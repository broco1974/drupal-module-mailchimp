<?php
/**
 * @file
 * Contains \Drupal\mailchimp_signup\Routing\MailchimpSignupRoutes.
 */

namespace Drupal\mailchimp_signup\Routing;

use Symfony\Component\Routing\Route;

/**
 * Defines dynamic routes for MailChimp signup forms rendered as pages.
 */
class MailchimpSignupRoutes {

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $routes = array();

    $signups = mailchimp_signup_load_multiple();

    /* @var $campaign \Drupal\mailchimp_signup\Entity\MailchimpSignup */
    foreach ($signups as $signup) {
      if ((intval($signup->mode) == MAILCHIMP_SIGNUP_PAGE) || (intval($signup->mode) == MAILCHIMP_SIGNUP_BOTH)) {
        $routes['mailchimp_signup.' . $signup->id] = new Route(
          // Route Path.
          '/' . $signup->settings['path'],
          // Route defaults.
          array(
            '_form' => '\Drupal\mailchimp_signup\Form\MailchimpSignupPageForm',
            '_title' => $signup->title,
          ),
          // Route requirements.
          array(
            '_permission'  => 'access mailchimp signup pages',
          )
        );
      }
    }

    return $routes;
  }

}
