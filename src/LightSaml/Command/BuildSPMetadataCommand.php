<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Command;

use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\Metadata\SingleLogoutService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\SamlConstants;
use LightSaml\Credential\X509Certificate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

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
        /** @var $questionHelper QuestionHelper */
        $questionHelper = $this->getHelperSet()->get('question');

        $entityID = $questionHelper->ask($input, $output, $this->buildEntityIDQuestion());

        $ed = new EntityDescriptor($entityID);
        $sp = new SpSsoDescriptor();
        $ed->addItem($sp);

        $this->askForCertificate($questionHelper, $input, $output, $sp);

        $output->writeln('');

        $assertionsSignedQuestion = new ConfirmationQuestion('Want assertions signed [yes]: ', true);
        $sp->setWantAssertionsSigned($questionHelper->ask($input, $output, $assertionsSignedQuestion));

        $output->writeln('');

        $this->askForSLO($questionHelper, $input, $output, $sp);

        $output->writeln('');

        $this->askForACS($questionHelper, $input, $output, $sp);

        $output->writeln('');

        $outputFile = $questionHelper->ask($input, $output, $this->buildOutputFileQuestion());

        $formatOutputQuestion = new ConfirmationQuestion('Format output xml [no]: ', false);
        $formatOutput = $questionHelper->ask($input, $output, $formatOutputQuestion);

        $context = new SerializationContext();
        $context->getDocument()->formatOutput = $formatOutput;
        $ed->serialize($context->getDocument(), $context);
        $xml = $context->getDocument()->saveXML();
        file_put_contents($outputFile, $xml);
    }

    /**
     * @param QuestionHelper  $questionHelper
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SpSsoDescriptor $sp
     */
    protected function askForCertificate(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, SpSsoDescriptor $sp)
    {
        $certificatePath = $questionHelper->ask($input, $output, $this->buildInputFileQuestion('Signing Certificate path', false));
        if ($certificatePath) {
            $certificate = new X509Certificate();
            $certificate->loadFromFile($certificatePath);
            $keyDescriptor = new KeyDescriptor('signing', $certificate);
            $sp->addKeyDescriptor($keyDescriptor);
        }
    }

    /**
     * @param QuestionHelper  $questionHelper
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SpSsoDescriptor $sp
     */
    protected function askForSLO(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, SpSsoDescriptor $sp)
    {
        while (true) {
            list($url, $binding) = $this->askUrlBinding($questionHelper, $input, $output, 'Single Logout');
            if (!$url) {
                break;
            }
            $service = new SingleLogoutService();
            $service->setLocation($url);
            $service->setBinding($binding);
            $sp->addSingleLogoutService($service);
        }
    }

    /**
     * @param QuestionHelper  $questionHelper
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param SpSsoDescriptor $sp
     */
    protected function askForACS(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, SpSsoDescriptor $sp)
    {
        $index = 0;
        while (true) {
            list($url, $binding) = $this->askUrlBinding($questionHelper, $input, $output, 'Assertion Consumer Service');
            if (!$url) {
                break;
            }
            $service = new AssertionConsumerService();
            $service->setIsDefault($index == 0);
            $service->setIndex($index);
            $service->setBinding($binding);
            $service->setLocation($url);
            $sp->addAssertionConsumerService($service);
            ++$index;
        }
    }

    /**
     * @param QuestionHelper  $questionHelper
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $title
     *
     * @return array
     */
    protected function askUrlBinding(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, $title)
    {
        $urlQuestion = new Question(sprintf('%s URL [empty for none]: ', $title));
        $url = trim($questionHelper->ask($input, $output, $urlQuestion));
        if (!$url) {
            return array(null, null);
        }

        $bindings = array(
            'post' => SamlConstants::BINDING_SAML2_HTTP_POST,
            'redirect' => SamlConstants::BINDING_SAML2_HTTP_REDIRECT,
        );
        $bindingQuestion = new ChoiceQuestion('Binding [post]: ', array_keys($bindings));
        $bindingChoice = $questionHelper->ask($input, $output, $bindingQuestion);

        return array($url, $bindings[$bindingChoice]);
    }

    /**
     * @return Question
     */
    protected function buildEntityIDQuestion()
    {
        $question = new Question('EntityID [https://example.com/saml]: ', 'https://example.com/saml');
        $question->setValidator(function ($answer) {
            $answer = trim($answer);
            if (false == $answer) {
                throw new \RuntimeException('EntityID can not be empty');
            }

            return $answer;
        });

        return $question;
    }

    /**
     * @param string $title
     * @param bool   $required
     *
     * @return Question
     */
    protected function buildInputFileQuestion($title, $required)
    {
        $question = new Question(sprintf('%s%s: ', $title, (!$required ? ' [empty for none]' : '')));
        $question->setValidator(function ($answer) use ($required) {
            if (false == $required && false == $answer) {
                return null;
            }
            if (false == is_file($answer)) {
                throw new \RuntimeException('Specified file not found');
            }

            return $answer;
        });

        return $question;
    }

    /**
     * @param QuestionHelper  $questionHelper
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return string
     */
    protected function buildOutputFileQuestion()
    {
        $question = new Question('Save to filename [FederationMetadata.xml]: ', 'FederationMetadata.xml');
        $question->setValidator(function ($answer) {
            $answer = trim($answer);
            if (false == $answer) {
                throw new \RuntimeException('Filename can not be empty');
            }

            return $answer;
        });

        return $question;
    }
}
