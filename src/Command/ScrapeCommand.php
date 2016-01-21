<?php

namespace Test\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Test\Scraper\Scraper;

class ScrapeCommand extends Command
{
    /**
     * @var Test\Scraper\Scraper
     */
    private $scraper;

    /**
     * @param Test\Scraper\Scraper $scraper
     */
    public function __construct(Scraper $scraper)
    {
        $this->scraper = $scraper;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this->setName('product:scrape')
          ->setDescription('Scrape a URL for products')
          ->addArgument('url', InputArgument::REQUIRED, 'URL to scrape')
          ->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Output results to file');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $file = $input->getOption('file');
        if (!\filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \RuntimeException('Please specify a valid URL');
        }
        if ($file && !\touch($file)) {
            throw new \RuntimeException('Please specify a writeable file path');
        }
        $collection = $this->scraper->scrape($url, $output);
        if ($file) {
            \file_put_contents($file, (string)$collection);
        } else {
            $output->write((string)$collection);
        }
    }
}
