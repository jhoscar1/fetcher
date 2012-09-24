<?php

namespace Fetcher\DBSynchronizer;
use Symfony\Component\Process\Process;

class DrushSqlSync implements DBSynchronizerInterface {

  protected $container = NULL;

  public function __construct(Pimple $container) {
    $this->container = $container;
  }

  public function syncDB() {
    // Don't hard code this and rework all of it to work properly with aliases.
    $commandline_options = array(
      '--no-ordered-dump',
      '--yes',
    );
    if ($this->container['verbose']) {
      $commandline_options[] = '--verbose';
    }
    $commandline_args = array(
      // TODO: Support multisite?
      // TODO: Get this dynamically.
      '@' . $this->container['name'] . '.' . $this->container['environment'],
      '@' . $this->container['name'] . '.local',
    );
    if ($this->container['verbose']) {
      $command = 'drush sql-sync ' . implode(' ', $commandline_args) . ' ' . implode(' ', $commandline_options);
      drush_log(dt('Executing: `!command`. ', array('!command' => $command)), 'ok');
    }
    if (!drush_invoke_process($commandline_args[1], 'sql-sync', $commandline_args, $commandline_options)) {
      throw new \Fetcher\Exception\FetcherException('Database syncronization FAILED!');
    }
  }
}
