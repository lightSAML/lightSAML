<?php

namespace LightSaml\Command;

use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\Metadata\SingleLogoutService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\SamlConstants;
use LightSaml\Model\Security\X509Certificate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildSPMetadataCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('lightsaml:sp:meta:build')
            ->setDescription('Builds SP metadata xml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var  $dialog DialogHelper */
        $dialog = $this->getHelperSet()->get('dialog');

        $entityID = $this->askForEntityID($dialog, $output);

        $ed = new EntityDescriptor($entityID);
        $sp = new SpSsoDescriptor();
        $ed->addItem($sp);

        $this->askForCertificate($dialog, $output, $sp);

        $output->writeln('');

        $wantAssertionsSigned = (bool) $dialog->select($output, 'Want assertions signed [yes]: ', array('no', 'yes'), 1);
        $sp->setWantAssertionsSigned($wantAssertionsSigned);

        $output->writeln('');

        $this->askForSLO($dialog, $output, $sp);

        $output->writeln('');

        $this->askForACS($dialog, $output, $sp);

        $output->writeln('');

        $filename = $this->askForFilename($dialog, $output);

        $formatOutput = $dialog->select($output, 'Format output xml [no]: ', array('no', 'yes'), 0);

        $context = new SerializationContext();
        $context->getDocument()->formatOutput = (bool) $formatOutput;
        $ed->serialize($context->getDocument(), $context);
        $xml = $context->getDocument()->saveXML();
        file_put_contents($filename, $xml);
    }

    /**
     * @param DialogHelper    $dialog
     * @param OutputInterface $output
     *
     * @return string
     */
    protected function askForEntityID(DialogHelper $dialog, OutputInterface $output)
    {
        $entityID = $dialog->askAndValidate($output, 'EntityID [https://example.com/saml]: ', function ($answer) {
            $answer = trim($answer);
            if (false == $answer) {
                throw new \RuntimeException('EntityID can not be empty');
            }

            return $answer;
        }, false, 'https://example.com/saml');

        return $entityID;
    }

    /**
     * @param DialogHelper    $dialog
     * @param OutputInterface $output
     * @param SpSsoDescriptor $sp
     */
    protected function askForCertificate(DialogHelper $dialog, OutputInterface $output, SpSsoDescriptor $sp)
    {
        $certificatePath = $this->askFile($dialog, $output, 'Signing Certificate path', false);
        if ($certificatePath) {
            $certificate = new X509Certificate();
            $certificate->loadFromFile($certificatePath);
            $keyDescriptor = new KeyDescriptor('signing', $certificate);
            $sp->addKeyDescriptor($keyDescriptor);
        }
    }

    /**
     * @param DialogHelper    $dialog
     * @param OutputInterface $output
     * @param SpSsoDescriptor $sp
     */
    protected function askForSLO(DialogHelper $dialog, OutputInterface $output, SpSsoDescriptor $sp)
    {
        while (true) {
            list($url, $binding) = $this->askUrlBinding($dialog, $output, 'Single Logout');
            if (!$url) {
                break;
            }
            $s = new SingleLogoutService();
            $s->setLocation($url);
            $s->setBinding($this->resolveBinding($binding));
            $sp->addSingleLogoutService($s);
            break;
        }
    }

    /**
     * @param DialogHelper    $dialog
     * @param OutputInterface $output
     * @param SpSsoDescriptor $sp
     */
    protected function askForACS(DialogHelper $dialog, OutputInterface $output, SpSsoDescriptor $sp)
    {
        $index = 0;
        while (true) {
            list($url, $binding) = $this->askUrlBinding($dialog, $output, 'Assertion Consumer Service');
            if (false == $url) {
                break;
            }
            $s = (new AssertionConsumerService())
                ->setBinding($this->resolveBinding($binding))
                ->setLocation($url)
                ->setIsDefault($index == 0)
                ->setIndex($index++)
            ;
            $sp->addAssertionConsumerService($s);
        }
    }

    /**
     * @param DialogHelper    $dialog
     * @param OutputInterface $output
     *
     * @return string
     */
    protected function askForFilename(DialogHelper $dialog, OutputInterface $output)
    {
        $filename = $dialog->askAndValidate(
            $output,
            'Save to filename [FederationMetadata.xml]: ',
            function ($answer) {
                $answer = trim($answer);
                if (false == $answer) {
                    throw new \RuntimeException('Filename can not be empty');
                }

                return $answer;
            },
            false,
            'FederationMetadata.xml'
        );

        return $filename;
    }

    /**
     * @param string $binding
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function resolveBinding($binding)
    {
        switch ($binding) {
            case 'post':
                return SamlConstants::BINDING_SAML2_HTTP_POST;
            case 'redirect':
                return SamlConstants::BINDING_SAML2_HTTP_REDIRECT;
            default:
                throw new \RuntimeException(sprintf("Unknown binding '%s'", $binding));
        }
    }

    /**
     * @param DialogHelper    $dialog
     * @param OutputInterface $output
     * @param string          $title
     * @param bool            $required
     *
     * @return string
     */
    protected function askFile(DialogHelper $dialog, OutputInterface $output, $title, $required)
    {
        $result = $dialog->askAndValidate(
            $output,
            "$title [empty for none]: ",
            function ($answer) use ($required) {
                if (false == $required && false == $answer) {
                    return;
                }
                if (false == is_file($answer)) {
                    throw new \RuntimeException('Specified file not found');
                }

                return $answer;
            }
        );

        return $result;
    }

    /**
     * @param DialogHelper    $dialog
     * @param OutputInterface $output
     * @param string          $title
     *
     * @return array
     */
    protected function askUrlBinding(DialogHelper $dialog, OutputInterface $output, $title)
    {
        $url = $dialog->ask($output, sprintf("%s URL [empty for none]: ", $title));
        $url = trim($url);
        if (!$url) {
            return array(null, null);
        }

        $arrBindings = array('post', 'redirect');
        $binding = $dialog->select($output, 'Binding: ', $arrBindings, 'post');
        $binding = $arrBindings[$binding];

        return array($url, $binding);
    }
}
