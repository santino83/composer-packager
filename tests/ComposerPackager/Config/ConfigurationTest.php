<?php

/*
 *  Voiptech s.r.l. 2017-2020
 */


namespace Santino83\ComposerPackager\Config;

use PHPUnit\Framework\TestCase;
use Santino83\ComposerPackager\Utils\ConfigProcessor;
use Symfony\Component\Yaml\Yaml;

/**
 * Description of ConfigurationTest
 *
 * @author <a href="mailto:gsantini@voiptech.it">Giorgio M. Santini</a>
 */
class ConfigurationTest extends TestCase
{

    public function testNormal(): void
    {

        $config = Yaml::parse(
            file_get_contents(ASSETS_DIR . '/sample-config.yml')
        );

        $processedConfig = ConfigProcessor::processConfiguration(new Configuration(), $config);

        $this->assertNotNull($processedConfig);
        $this->assertTrue(is_array($processedConfig));
        $this->assertArrayHasKey('composer', $processedConfig);
        $this->assertArrayHasKey('name', $processedConfig);
        $this->assertArrayHasKey('repositories', $processedConfig);
        $this->assertCount(1, $processedConfig['repositories']);
        $this->assertArrayHasKey('ARTIFACTORY', $processedConfig['repositories']);
        $repositoryConfig = $processedConfig['repositories']['ARTIFACTORY'];
        $this->assertArrayHasKey('endpoint', $repositoryConfig);
        $this->assertArrayHasKey('username', $repositoryConfig);
        $this->assertArrayHasKey('password', $repositoryConfig);

        $this->assertEquals('./composer.json', $processedConfig['composer']);
        $this->assertEquals('test-name', $processedConfig['name']);
        $this->assertEquals('https://api.endpoint.it', $repositoryConfig['endpoint']);
        $this->assertEquals('AN_USERNAME', $repositoryConfig['username']);
        $this->assertEquals('A_PASSWORD', $repositoryConfig['password']);

    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Unrecognized option "invalidparam"
     */
    public function testInvalid(): void
    {
        $config = Yaml::parse(
            file_get_contents(ASSETS_DIR . '/sample-config-invalid.yml')
        );

        $processedConfig = ConfigProcessor::processConfiguration(new Configuration(), $config);
    }

}
