<?php
/**
 * Created by PhpStorm.
 * User: santino83
 * Date: 06/07/18
 * Time: 17.55
 */

namespace Santino83\ComposerPackager\Repository;


use PHPUnit\Framework\TestCase;
use Santino83\ComposerPackager\Archiver\Archive;
use Santino83\ComposerPackager\Package\Package;
use Santino83\ComposerPackager\Repository\ARTIFACTORY\ArtifactoryRepository;

class ArtifactoryRepositoryTest extends TestCase
{

    public function testSupport(): void
    {
        $repo = new ArtifactoryRepository();
        $this->assertTrue($repo->support('artifactory'));
        $this->assertTrue($repo->support('ARTIFACTORY'));
    }

    /**
     * @expectedException \Santino83\ComposerPackager\Exception\RepositoryConfigException
     */
    public function testValidate_error(): void
    {
        $config = [
            'invalidparam' => 'invalidvalue'
        ];

        $repo = new ArtifactoryRepository();
        $repo->publish(new Package("a"), $config);
    }

    public function testPublish(): void
    {
        $archive = new Archive(ASSETS_DIR.'/sample-asset.tar');
        $package = new Package("test/test-packager", '1.0.0', $archive, false);

        $config = [
            'endpoint' => ARTIFACTORY_ENDPOINT,
            'username' => ARTIFACTORY_USERNAME,
            'password' => ARTIFACTORY_PASSWORD
        ];

        $repo = new ArtifactoryRepository();
        $this->assertTrue($repo->publish($package, $config));
    }

    public function testPublishDev(): void
    {
        $archive = new Archive(ASSETS_DIR.'/sample-asset.tar');
        $package = new Package("test/test-packager", '1.0.0', $archive, true);

        $config = [
            'endpoint' => ARTIFACTORY_ENDPOINT,
            'username' => ARTIFACTORY_USERNAME,
            'password' => ARTIFACTORY_PASSWORD
        ];

        $repo = new ArtifactoryRepository();
        $this->assertTrue($repo->publish($package, $config));
    }

}