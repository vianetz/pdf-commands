<?php
/**
 * Generate PDF command
 *
 * @section LICENSE
 * This file is created by vianetz <info@vianetz.com>.
 * The code is distributed under the GPL license.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@vianetz.com so we can send you a copy immediately.
 *
 * @category    Vianetz
 * @package     Vianetz\PdfCommands
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        https://www.vianetz.com
 * @copyright   Copyright (c) since 2018 vianetz - Dipl.-Ing. C. Massmann (http://www.vianetz.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE
 */

namespace Vianetz\PdfCommands\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vianetz\PdfCommands\Document;
use Vianetz\PdfCommands\EventManager;
use Vianetz\Pdf\Model\Config;
use Vianetz\Pdf\Model\Generator\Dompdf;
use Vianetz\Pdf\Model\Merger\Fpdi;
use Vianetz\Pdf\Model\Pdf;

class GeneratePdfCommand extends Command
{
    protected function configure()
    {
        $this->setName('pdf:generate')
            ->setDescription('Generates the pdf file from html input.')
            ->addArgument('output-filename', InputArgument::REQUIRED)
            ->addArgument('input-filename', InputArgument::IS_ARRAY | InputArgument::REQUIRED)
            ->addOption('paper-orientation', 'o', InputOption::VALUE_OPTIONAL, '', Config::PAPER_ORIENTATION_PORTRAIT)
            ->addOption('paper-size', 's', InputOption::VALUE_OPTIONAL, '', 'a4')
            ->setHelp('This command allows you to create a pdf file from given input file(s).');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('output-filename');
        $paperOrientation = $input->getOption('paper-orientation');
        if (empty($paperOrientation) === true || ($paperOrientation !== Config::PAPER_ORIENTATION_PORTRAIT && $paperOrientation !== Config::PAPER_ORIENTATION_LANDSCAPE)) {
            $paperOrientation = Config::PAPER_ORIENTATION_PORTRAIT;
        }
        $paperSize = $input->getOption('paper-size');

        $io = new SymfonyStyle($input, $output);

        try {
            $config = new Config();
            $config->setPdfOrientation($paperOrientation);
            $config->setPdfSize($paperSize);

            $eventManager = new EventManager();
            $generator = new Dompdf($config);
            $merger = new Fpdi($config);

            $pdf = new Pdf($config, $eventManager, $generator, $merger);

            foreach ($input->getArgument('input-filename') as $inputFilename) {
                $document = new Document();
                $document->setInputFile($inputFilename);

                $pdf->addDocument($document);
            }

            if ($pdf->saveToFile($filename) === false) {
                throw new \Exception('PDF generation error.');
            }

            $io->success(sprintf('PDF "%s" successfully generated.', $filename));
        } catch (\Exception $exception) {
            $io->error('PDF file cannot be generated: ' . $exception->getMessage());
        }
    }
}