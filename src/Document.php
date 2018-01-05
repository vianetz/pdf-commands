<?php
/**
 * Document Class
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

namespace Vianetz\PdfCommands;

use Vianetz\Pdf\Model\DocumentInterface;

class Document implements DocumentInterface
{
    /**
     * @var string
     */
    private $inputFile;

    /**
     * @return bool|string
     */
    public function getHtmlContents()
    {
        return @file_get_contents($this->inputFile);
    }

    /**
     * @param string $inputFile
     *
     * @return $this
     * @throws \Vianetz\PdfCommands\FileNotFoundException
     */
    public function setInputFile($inputFile)
    {
        $fileWithPath = realpath($inputFile);
        if (@file_exists($fileWithPath) === false) {
            throw new FileNotFoundException('Input file ' . $fileWithPath . ' was not found.');
        }

        $this->inputFile = $fileWithPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getPdfBackgroundFile()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getPdfBackgroundFileForFirstPage()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getPdfAttachmentFile()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getDocumentType()
    {
        return '';
    }
}