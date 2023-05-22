<?php

namespace Drush\Commands;

use Consolidation\AnnotatedCommand\AnnotationData;
use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\AnnotatedCommand\Events\CustomEventAwareInterface;
use Consolidation\AnnotatedCommand\Events\CustomEventAwareTrait;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Consolidation\SiteAlias\SiteAliasManagerAwareTrait;
use Consolidation\SiteAlias\SiteAliasTrait;
use Drush\SiteAlias\SiteAliasManagerAwareInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Drush\Style\DrushStyle;
use Drush\Utils\StringUtils;

/**
 *
 */

class BehatCommands extends DrushCommands implements CustomEventAwareInterface, SiteAliasManagerAwareInterface
{
  use CustomEventAwareTrait;
  use SiteAliasManagerAwareTrait;

  /**
   * Run bin/behat
   *
   * @command behat
   * @usage drush behat
   *   Run behat tests with info from the alias.
   */
  public function behat($options = [])
  {
    $behat_params = [
      "extensions" => [
        "Drupal\\MinkExtension" => [
          "base_url" => $this->commandData->options()['uri'],
        ],
        "Drupal\\DrupalExtension" => [
          "drupal" => [
            "drupal_root" => $this->commandData->options()['root']
          ],
          "drush" => [
            "alias" =>  $this->siteAliasManager()->getSelf()->name(),
          ]
        ]
      ]
    ];

    $env = [
      "BEHAT_PARAMS" => json_encode($behat_params),
    ];

    $this->logger()->notice("Detected URL and root from Drush:");
    $this->logger()->notice($this->commandData->options()['uri']);
    $this->logger()->notice($this->commandData->options()['root']);
    $this->logger()->notice($this->siteAliasManager()->getSelf()->name());

    $this->processManager()->shell('bin/behat --colors', NULL, $env)->mustRun(function ($type, $buffer) {
        echo $buffer;
      }
    );
  }
}
