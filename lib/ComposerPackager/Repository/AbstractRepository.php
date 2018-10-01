<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 27/09/18
 * Time: 17.46
 */

namespace Santino83\ComposerPackager\Repository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function GuzzleHttp\Psr7\stream_for;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Santino83\ComposerPackager\Exception\RepositoryConfigException;
use Santino83\ComposerPackager\Package\PackageInterface;

/**
 * Class AbstractRepository
 * @package Santino83\ComposerPackager\Repository
 */
abstract class AbstractRepository implements RepositoryInterface
{

    /**
     * @inheritdoc
     */
    public function publish(PackageInterface $package, array $repositoryConfig): bool
    {
        $this->validateConfiguration($repositoryConfig);

        return $this->doPublish($package, $repositoryConfig);
    }

    /**
     * Checks if the package has been correctly published into the remote repository
     *
     * @param ResponseInterface $response the response coming from the remote repository
     * @return bool true/false
     */
    abstract protected function validateResponse(ResponseInterface $response): bool;

    /**
     * Gets the computed remote uri for this repository
     *
     * @param PackageInterface $package the package to be published
     * @param array $repositoryConfig the remote repository configuration
     * @return string the uri
     */
    abstract protected function getRemoteURI(PackageInterface $package, array $repositoryConfig): string;

    /**
     * @inheritdoc
     */
    public function support(string $type): bool
    {
        return strtoupper($type . '') == $this->getRepositoryType();
    }

    /**
     * Gets the artifact name that will appear in the remote repository
     *
     * @param PackageInterface $package the package to be published
     * @return string the artifact name
     */
    protected function getArtifactName(PackageInterface $package): string
    {
        return $package->getName().'-'.$package->getVersion().'.'.$package->getArchive()->getType();
    }

    /**
     * Publishes the package into the remote repository
     *
     * @param PackageInterface $package the package to be published
     * @param array $repositoryConfig the remote repository configuration
     * @return bool true/false
     * @throws RepositoryConfigException on publishing exception
     */
    protected function doPublish(PackageInterface $package, array $repositoryConfig): bool
    {
        $client = new Client();

        $remoteURI = $this->getRemoteURI($package, $repositoryConfig);

        $options = $this->getRequestOptions($repositoryConfig);
        $options[RequestOptions::BODY] = $this->getRequestBody($package);

        $method = $this->getRequestMethod();

        try {
            $response = $client->request($method, $remoteURI, $options);
        } catch (GuzzleException $e) {
            throw new RepositoryConfigException('Unable to publish to remote repistory: '.$e->getMessage(), 500, $e);
        }

        return $this->validateResponse($response);
    }

    /**
     * Gets the HTTP Method to be used in the HttpRequest
     *
     * @return string
     */
    protected function getRequestMethod(): string
    {
        return 'PUT';
    }

    /**
     * Gets the body to be sent via the HttpRequest
     *
     * @param PackageInterface $package the package to be published
     * @return mixed the body
     */
    protected function getRequestBody(PackageInterface $package)
    {
        return stream_for(fopen($package->getArchive()->getPath(), 'r'));
    }

    /**
     * Gets the options for the HttpRequests (auth/headers/etc...)
     *
     * @param array $repositoryConfig the remote repository configuration
     * @return array RequestOptions => value the options
     */
    protected function getRequestOptions(array $repositoryConfig): array
    {
        return [];
    }

    /**
     * Validates the remote repository configuration
     *
     * @param array $repositoryConfig the remote repository configuration
     * @return bool true/false
     * @throws RepositoryConfigException if validation fails
     */
    protected function validateConfiguration(array $repositoryConfig): bool
    {
        $validator = $this->getRepositoryConfigValidator();
        return $validator->validate($repositoryConfig);
    }

    /**
     * Gets the type of this repository (eg: ARTIFACTORY,...)
     *
     * @return string the type
     * @throws RepositoryConfigException if the type cannot be auto-determinated
     */
    protected function getRepositoryType(): string
    {
        try {
            $class = new \ReflectionClass($this);

            return strtoupper(str_replace('Repository', '', $class->getShortName()));
        } catch (\Exception $ex) {
            throw new RepositoryConfigException("Unable to determinate the type of this repository",500, $ex);
        }
    }

    /**
     * Gets the RepositoryConfigValidatorInterface to be used when validating the remote
     * repository configuration
     *
     * @return RepositoryConfigValidatorInterface the validator
     * @throws RepositoryConfigException if it cannot be auto-determinated/instantiated
     */
    protected function getRepositoryConfigValidator(): RepositoryConfigValidatorInterface
    {
        try {
            $class = new \ReflectionClass($this);
            $className = $class->getShortName();
            $ns = $class->getNamespaceName();

            $target = sprintf('%s\\%s', $ns, $className . 'ConfigValidator');

            return new $target();
        } catch (\Exception $ex) {
            throw new RepositoryConfigException("Unable to load Config validator for this repo", 500, $ex);
        }
    }

}