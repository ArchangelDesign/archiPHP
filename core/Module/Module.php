<?php

namespace Archi\Module;

use Archi\Environment\Env;
use Archi\Helper\Nomenclature;
use Archi\Module\Exception\InvalidLocalModule;

class Module
{
    private string $fileName;
    private string $name;
    private ?string $description;
    private ?string $minimumCoreVersion;
    private string $title;
    private string $version;
    private ?string $author;
    private string $directory;
    private string $namespace;
    private string $className;

    /**
     * Module constructor.
     * @param string $fileName
     * @param string $name
     * @param string|null $description
     * @param string|null $minimumCoreVersion
     * @param string $title
     * @param string $version
     * @param string|null $author
     * @param string $directory
     */
    public function __construct(
        string $fileName,
        string $name,
        ?string $description,
        ?string $minimumCoreVersion,
        string $title,
        string $version,
        ?string $author,
        string $directory
    ) {
        $this->fileName = $fileName;
        $this->name = $name;
        $this->description = $description;
        $this->minimumCoreVersion = $minimumCoreVersion;
        $this->title = $title;
        $this->version = $version;
        $this->author = $author;
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getNameInCamelCase(): string
    {
        return Nomenclature::toPascalCase($this->getName());
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getMinimumCoreVersion(): ?string
    {
        return $this->minimumCoreVersion;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getLoadFile(): string
    {
        return $this->directory . Env::ds() . $this->fileName;
    }

    /**
     * @throws InvalidLocalModule
     */
    public function preLoad()
    {
        $moduleFile = $this->getLoadFile();
        $contents = file_get_contents($moduleFile);
        if (!$this->hasNamespace($contents)) {
            throw new InvalidLocalModule(
                'Module ' . $this->getName() . ': ' . $moduleFile
                . ' does not have a namespace.'
            );
        }
        $this->namespace = $this->getNamespaceFromContencts($contents);
        $this->className = $this->getClassNameFromContents($contents);
    }

    /**
     * @param string $contents
     * @return false|int
     */
    private function hasNamespace(string $contents): bool
    {
        return (bool)preg_match('/namespace ([a-zA-Z\\\\]*);/im', $contents);
    }

    private function getNamespaceFromContencts(string $contents): string
    {
        preg_match('/namespace ([a-zA-Z\\\\]*);/im', $contents, $matches);
        return $matches[1];
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getClassName(): string
    {
        return '\\' . $this->namespace . '\\' . $this->className;
    }

    private function getClassNameFromContents(string $contents): string
    {
        if (!preg_match('/class ([A-Za-z0-9]*)/', $contents, $matches)) {
            throw new InvalidLocalModule('Module ' . $this->name . ' is not valid.');
        }

        return $matches[1];
    }

}
