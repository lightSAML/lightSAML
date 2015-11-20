<?php

namespace LightSaml\Tests\Command;

use LightSaml\Command\BuildSPMetadataCommand;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\SamlConstants;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class BuildSPMetadataCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test_builds_entity_descriptor_xml()
    {
        $file = tmpfile();
        $streamData = stream_get_meta_data($file);
        $application = new Application();
        $application->add(new BuildSPMetadataCommand());
        $command = $application->find('lightsaml:sp:meta:build');
        $commandTester = new CommandTester($command);

        $dialog = $command->getHelper('dialog');
        $dialog->setInputStream($this->getInputStream([
            $ownEntityId = 'http://own.id',
            __DIR__.'/../../../../resources/sample/Certificate/saml.crt',
            '',
            $sloUrl = 'http://localhost/slo',
            '1',
            $acsUrl = 'http://localhost/acs',
            '0',
            '',
            $streamData['uri'],
            '1',
            ''
        ]));

        // Equals to a user inputting "Test" and hitting ENTER
        // If you need to enter a confirmation, "yes\n" will work

        $commandTester->execute(array('command' => $command->getName()));

        $this->assertEquals(0, $commandTester->getStatusCode());

        fseek($file, 0);
        $xml = fread($file, 16000);

        $entityDescriptor = EntityDescriptor::loadXml($xml);

        $this->assertEquals($ownEntityId, $entityDescriptor->getEntityID());
        $this->assertCount(1, $entityDescriptor->getAllSpKeyDescriptors());

        $endpoint = $entityDescriptor->getFirstSpSsoDescriptor()->getFirstAssertionConsumerService();
        $this->assertEquals($acsUrl, $endpoint->getLocation());
        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_POST, $endpoint->getBinding());

        $endpoint = $entityDescriptor->getFirstSpSsoDescriptor()->getFirstSingleLogoutService();
        $this->assertEquals($sloUrl, $endpoint->getLocation());
        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_REDIRECT, $endpoint->getBinding());
    }

    protected function getInputStream($input)
    {
        if (is_array($input)) {
            $input = implode("\n", $input);
        }
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
