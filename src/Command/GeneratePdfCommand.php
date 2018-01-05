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
            ->setHelp('This command allows you to create a pdf file from given input file(s).');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('output-filename');
        $io = new SymfonyStyle($input, $output);

        try {
            $config = new Config();
            $eventManager = new EventManager();
            $generator = new Dompdf();
            $merger = new Fpdi();

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