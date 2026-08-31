<?php declare(strict_types = 1);
/*
 * This file is part of PharIo\Manifest.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Manifest;

use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraint;
use XMLWriter;
use function count;
use function file_put_contents;
use function str_repeat;

/** @psalm-suppress MissingConstructor */
class ManifestSerializer {
    /** @var XMLWriter */
    private $xmlWriter;

    public function serializeToFile(Manifest $manifest, string $filename): void {
        file_put_contents(
            $filename,
            $this->serializeToString($manifest)
        );
    }

    public function serializeToString(Manifest $manifest): string {
        $this->startDocument();

        $this->addContains($manifest->getName(), $manifest->getVersion(), $manifest->getType());
        $this->addCopyright($manifest->getCopyrightInformation());
        $this->addRequirements($manifest->getRequirements());
        $this->addBundles($manifest->getBundledComponents());

        return $this->finishDocument();
    }

    private function startDocument(): void {
        $xmlWriter = new XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->setIndent(true);
        $xmlWriter->setIndentString(str_repeat(' ', 4));
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('phar');
        $xmlWriter->writeAttribute('xmlns', 'https://phar.io/xml/manifest/1.0');

        $this->xmlWriter = $xmlWriter;
    }

    private function finishDocument(): string {
        $this->xmlWriter->endElement();
        $this->xmlWriter->endDocument();

        return $this->xmlWriter->outputMemory();
    }

    private function addContains(ApplicationName $name, Version $version, Type $type): void {
        $this->xmlWriter->startElement('contains');
        $this->xmlWriter->writeAttribute('name', $name->asString());
        $this->xmlWriter->writeAttribute('version', $version->getVersionString());

        switch (true) {
            case $type->isApplication(): {
                $this->xmlWriter->writeAttribute('type', 'application');

                break;
            }

            case $type->isLibrary(): {
                $this->xmlWriter->writeAttribute('type', 'library');

                break;
            }

            case $type->isExtension(): {
                $this->xmlWriter->writeAttribute('type', 'extension');
                /* @var $type Extension */
                $this->addExtension(
                    $type->getApplicationName(),
                    $type->getVersionConstraint()
                );

                break;
            }

            default: {
                $this->xmlWriter->writeAttribute('type', 'custom');
            }
        }

        $this->xmlWriter->endElement();
    }

    private function addCopyright(CopyrightInformation $copyrightInformation): void {
        $this->xmlWriter->startElement('copyright');

        foreach ($copyrightInformation->getAuthors() as $author) {
            $this->xmlWriter->startElement('author');
            $this->xmlWriter->writeAttribute('name', $author->getName());
            $this->xmlWriter->writeAttribute('email', $author->getEmail()->asString());
            $this->xmlWriter->endElement();
        }

        $license = $copyrightInformation->getLicense();

        $this->xmlWriter->startElement('license');
        $this->xmlWriter->writeAttribute('type', $license->getName());
        $this->xmlWriter->writeAttribute('url', $license->getUrl()->asString());
        $this->xmlWriter->endElement();

        $this->xmlWriter->endElement();
    }

    private function addRequirements(RequirementCollection $requirementCollection): void {
        $phpRequirement = new AnyVersionConstraint();
        $extensions     = [];

        foreach ($requirementCollection as $requirement) {
            if ($requirement instanceof PhpVersionRequirement) {
                $phpRequirement = $requirement->getVersionConstraint();

                continue;
            }

            if ($requirement instanceof PhpExtensionRequirement) {
                $extensions[] = $requirement->asString();
            }
        }

        $this->xmlWriter->startElement('requires');
        $this->xmlWriter->startElement('php');
        $this->xmlWriter->writeAttribute('version', $phpRequirement->asString());

        foreach ($extensions as $extension) {
            $this->xmlWriter->startElement('ext');
            $this->xmlWriter->writeAttribute('name', $extension);
            $this->xmlWriter->endElement();
        }

        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();
    }

    private function addBundles(BundledComponentCollection $bundledComponentCollection): void {
        if (count($bundledComponentCollection) === 0) {
            return;
        }
        $this->xmlWriter->startElement('bundles');

        foreach ($bundledComponentCollection as $bundledComponent) {
            $this->xmlWriter->startElement('component');
            $this->xmlWriter->writeAttribute('name', $bundledComponent->getName());
            $this->xmlWriter->writeAttribute('version', $bundledComponent->getVersion()->getVersionString());
            $this->xmlWriter->endElement();
        }

        $this->xmlWriter->endElement();
    }

    private function addExtension(ApplicationName $applicationName, VersionConstraint $versionConstraint): void {
        $this->xmlWriter->startElement('extension');
        $this->xmlWriter->writeAttribute('for', $applicationName->asString());
        $this->xmlWriter->writeAttribute('compatible', $versionConstraint->asString());
        $this->xmlWriter->endElement();
    }
}
