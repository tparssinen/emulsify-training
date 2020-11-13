<?php

namespace Drupal\warden\Commands;

use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class WardenCommands extends DrushCommands {

  /**
   * Test the connection to Warden by getting its public key
   *
   *
   * @command warden:check
   * @aliases warden-check
   */
  public function check() {
    try {
      $warden = \Drupal::service('warden.manager');
      $this->logger->log('ok', dt('URL: :url', [':url' => $warden->getWardenUrl()]));

      if ($warden->hasBasicAuthentication()) {
        $this->logger->log('ok', dt('HTTP Username: :username', [':username' => $warden->getUsername()]));
        $this->logger->log('ok', dt('HTTP Password: :password', [':password' => $warden->getPassword()]));
      }

      if ($warden->hasCertificatePath()) {
        $this->logger->log('ok', dt('Certificate file: :path', [':path' => $warden->getCertificatePath()]));
      }

      $key = $warden->getPublicKey();

      $this->logger->log('ok', dt('Going to check connection to Warden server by retrieving the public key ...'));
      $this->logger->log('ok', $key);
    }
    catch (Exception $e) {
      $this->logger->error('ok', $e->getMessage());
    }
  }

  /**
   * Update Warden with the lastest site data
   *
   *
   * @command warden:update
   * @aliases warden-update
   */
  public function update() {
    $this->logger->log('ok', dt('Going to update Warden ...'));

    try {
      $warden_manager = \Drupal::service('warden.manager');
      $warden_manager->updateWarden();
      $this->logger->log('ok', dt('... success'));
    }
    catch (\Exception $e) {
      $this->logger->error('ok', $e->getMessage());
    }
  }

  /**
   * Displays the module data that will be sent to Warden
   *
   *
   * @command warden:show-module-data
   * @aliases warden-show-module-data
   */
  public function showModuleData() {
    module_load_include('inc', 'warden', 'warden.page');

    /** @var \Drupal\warden\Service\WardenManager $warden_manager */
    $warden_manager = \Drupal::service('warden.manager');
    $data = $warden_manager->generateSiteData();
    $this->output()->writeln(json_encode($data));
  }

}
