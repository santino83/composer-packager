<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 02/10/18
 * Time: 12.13
 */

namespace Santino83\ComposerPackager\Repository;


use Santino83\ComposerPackager\Repository\ARTIFACTORY\ArtifactoryRepository;

class RepositoryLoader
{

    private $definitions = [];

    /**
     * RepositoryLoader constructor.
     * @param array $definitions
     */
    public function __construct(array $definitions = [])
    {
        $this->loadDefaultDefinitions();

        foreach($definitions as $type => $class){
            $this->addDefinition($type, $class);
        }
    }

    /**
     * Adds a definition
     *
     * @param string $type
     * @param string $class
     * @return RepositoryLoader
     */
    public function addDefinition(string $type, string $class): RepositoryLoader
    {
        $this->definitions[$type] = $class;
        return $this;
    }

    /**
     * Loads a repository
     *
     * @param string $type the repository type
     * @return RepositoryInterface the repository
     * @throws \RuntimeException if cannot load the repository
     */
    public function load(string $type): RepositoryInterface
    {
        try {
            $class = $this->resolveRepositoryClass($type);
            return new $class();
        }catch (\Exception $e){
            throw new \RuntimeException('Unable to load repository of type '.$type.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $type
     * @return string
     */
    protected function resolveRepositoryClass(string $type): string
    {
        return array_key_exists($type, $this->definitions) ? $this->definitions[$type] : '';
    }

    private function loadDefaultDefinitions(): void
    {
        $defaults = [
            'ARTIFACTORY' => ArtifactoryRepository::class
        ];

        foreach($defaults as $type => $class)
        {
            $this->addDefinition($type, $class);
        }
    }

}