<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 06/07/18
 * Time: 16.16
 */

namespace Santino83\ComposerPackager\Repository\ARTIFACTORY;


use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Santino83\ComposerPackager\Package\PackageInterface;
use Santino83\ComposerPackager\Repository\AbstractRepository;

/**
 * Class ArtifactoryRepository
 * @package Santino83\ComposerPackager\Repository\ARTIFACTORY
 */
class ArtifactoryRepository extends AbstractRepository
{

    /**
     * @inheritdoc
     */
    protected function validateResponse(ResponseInterface $response): bool
    {
        return $response->getStatusCode() == 201;
    }

    /**
     * @inheritdoc
     */
    protected function getRemoteURI(PackageInterface $package, array $repositoryConfig): string
    {
        $artifactName = $this->getArtifactName($package);

        return sprintf('%s/%s;composer.version=%s',
            $repositoryConfig['endpoint'],
            $artifactName,
            $package->getVersion());
    }

    /**
     * @inheritdoc
     */
    protected function getRequestOptions(array $repositoryConfig): array
    {
        return [
            RequestOptions::AUTH => [$repositoryConfig['username'], $repositoryConfig['password']]
        ];
    }

}