<?php

namespace LightSaml\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Error\LightSamlValidationException;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Metadata\EntityDescriptor;

class RecipientValidatorAction extends AbstractAssertionAction
{
    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        $ownEntityDescriptor = $context->getProfileContext()->getOwnEntityDescriptor();

        if ($context->getAssertion()->getAllAuthnStatements() && $context->getAssertion()->hasBearerSubject()) {
            $this->validateBearerAssertion($context->getAssertion(), $ownEntityDescriptor);
        }
    }

    /**
     * @param Assertion        $assertion
     * @param EntityDescriptor $ownEntityDescriptor
     */
    protected function validateBearerAssertion(Assertion $assertion, EntityDescriptor $ownEntityDescriptor)
    {
        foreach ($assertion->getSubject()->getBearerConfirmations() as $subjectConfirmation) {
            $this->validateSubjectConfirmation($subjectConfirmation, $ownEntityDescriptor);
        }
    }

    /**
     * @param SubjectConfirmation $subjectConfirmation
     * @param EntityDescriptor    $ownEntityDescriptor
     */
    protected function validateSubjectConfirmation(SubjectConfirmation $subjectConfirmation, EntityDescriptor $ownEntityDescriptor)
    {
        $recipient = $subjectConfirmation->getSubjectConfirmationData()->getRecipient();
        if (null == $recipient) {
            throw new LightSamlValidationException('Bearer SubjectConfirmation must contain Recipient attribute');
        }

        $ok = false;
        foreach ($ownEntityDescriptor->getAllSpSsoDescriptors() as $spSsoDescriptor) {
            if ($spSsoDescriptor->getAllAssertionConsumerServicesByUrl($recipient)) {
                $ok = true;
                break;
            }
        }

        if (false === $ok) {
            throw new LightSamlValidationException(sprintf("Recipient '%s' does not match SP descriptor", $recipient));
        }
    }
}
