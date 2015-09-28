<?php

namespace LightSaml\Tests\Binding;

use LightSaml\Binding\BindingFactory;
use LightSaml\SamlConstants;
use Symfony\Component\HttpFoundation\Request;

class BindingFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateHttpRedirect()
    {
        $factory = new BindingFactory();
        $binding = $factory->create(SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
        $this->assertInstanceOf('LightSaml\Binding\HttpRedirectBinding', $binding);
    }

    public function testCreateHttpPost()
    {
        $factory = new BindingFactory();
        $binding = $factory->create(SamlConstants::BINDING_SAML2_HTTP_POST);
        $this->assertInstanceOf('LightSaml\Binding\HttpPostBinding', $binding);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage SOAP binding not implemented
     */
    public function testCreateThrowsNotImplementedErrorForSoap()
    {
        $factory = new BindingFactory();
        $factory->create(SamlConstants::BINDING_SAML2_SOAP);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Artifact binding not implemented
     */
    public function testCreateThrowsNotImplementedErrorForArtifact()
    {
        $factory = new BindingFactory();
        $factory->create(SamlConstants::BINDING_SAML2_HTTP_ARTIFACT);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlBindingException
     * @expectedExceptionMessage Unknown binding type 'foo'
     */
    public function testCreateThrowsForUnknownBinding()
    {
        $factory = new BindingFactory();
        $factory->create('foo');
    }

    public function testDetectHttpRedirect()
    {
        $request = $this->createHttpRedirectRequest();

        $factory = new BindingFactory();

        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_REDIRECT, $factory->detectBindingType($request));
    }

    public function testDetectHttpPost()
    {
        $request = $this->createHttpPostRequest();

        $factory = new BindingFactory();

        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_POST, $factory->detectBindingType($request));
    }

    public function testDetectArtifactPost()
    {
        $request = $this->createArtifactPostRequest();

        $factory = new BindingFactory();

        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_ARTIFACT, $factory->detectBindingType($request));
    }

    public function testDetectArtifactGet()
    {
        $request = $this->createArtifactGetRequest();

        $factory = new BindingFactory();

        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_ARTIFACT, $factory->detectBindingType($request));
    }

    public function testDetectSoap()
    {
        $request = $this->createSoapRequest();

        $factory = new BindingFactory();

        $this->assertEquals(SamlConstants::BINDING_SAML2_SOAP, $factory->detectBindingType($request));
    }

    public function testDetectNoneGet()
    {
        $request = new Request();
        $request->setMethod('GET');

        $factory = new BindingFactory();

        $this->assertNull($factory->detectBindingType($request));
    }

    public function testDetectNonePost()
    {
        $request = new Request();
        $request->setMethod('POST');

        $factory = new BindingFactory();

        $this->assertNull($factory->detectBindingType($request));
    }

    public function testGetBindingByRequestHttpRedirect()
    {
        $request = $this->createHttpRedirectRequest();
        $factory = new BindingFactory();
        $this->assertInstanceOf('LightSaml\Binding\HttpRedirectBinding', $factory->getBindingByRequest($request));
    }

    public function testGetBindingByRequestHttpPost()
    {
        $request = $this->createHttpPostRequest();
        $factory = new BindingFactory();
        $this->assertInstanceOf('LightSaml\Binding\HttpPostBinding', $factory->getBindingByRequest($request));
    }

    /**
     * @return Request
     */
    private function createHttpPostRequest()
    {
        $request = new Request();
        $request->request->add(array('SAMLRequest' => 'request'));
        $request->setMethod('POST');

        return $request;
    }

    /**
     * @return Request
     */
    private function createHttpRedirectRequest()
    {
        $request = new Request();
        $request->query->add(array('SAMLRequest' => 'request'));
        $request->setMethod('GET');

        return $request;
    }

    /**
     * @return Request
     */
    private function createArtifactPostRequest()
    {
        $request = new Request();
        $request->request->add(array('SAMLart' => 'request'));
        $request->setMethod('POST');

        return $request;
    }

    /**
     * @return Request
     */
    private function createArtifactGetRequest()
    {
        $request = new Request();
        $request->query->add(array('SAMLart' => 'request'));
        $request->setMethod('GET');

        return $request;
    }

    /**
     * @return Request
     */
    private function createSoapRequest()
    {
        $request = new Request();
        $request->setMethod('POST');
        $request->headers->add(array('CONTENT_TYPE' => 'text/xml; charset=utf-8'));

        return $request;
    }
}
