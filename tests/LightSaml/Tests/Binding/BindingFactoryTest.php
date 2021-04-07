<?php

namespace LightSaml\Tests\Binding;

use LightSaml\Binding\BindingFactory;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;

class BindingFactoryTest extends BaseTestCase
{
    public function test__create_http_redirect()
    {
        $factory = new BindingFactory();
        $binding = $factory->create(SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
        $this->assertInstanceOf('LightSaml\Binding\HttpRedirectBinding', $binding);
    }

    public function test__create_http_post()
    {
        $factory = new BindingFactory();
        $binding = $factory->create(SamlConstants::BINDING_SAML2_HTTP_POST);
        $this->assertInstanceOf('LightSaml\Binding\HttpPostBinding', $binding);
    }

    public function test__create_throws_not_implemented_error_for_soap()
    {
        $this->expectExceptionMessage("SOAP binding not implemented");
        $this->expectException(\LogicException::class);
        $factory = new BindingFactory();
        $factory->create(SamlConstants::BINDING_SAML2_SOAP);
    }

    public function test__create_throws_not_implemented_error_for_artifact()
    {
        $this->expectExceptionMessage("Artifact binding not implemented");
        $this->expectException(\LogicException::class);
        $factory = new BindingFactory();
        $factory->create(SamlConstants::BINDING_SAML2_HTTP_ARTIFACT);
    }

    public function test__create_throws_for_unknown_binding()
    {
        $this->expectExceptionMessage("Unknown binding type 'foo'");
        $this->expectException(\LightSaml\Error\LightSamlBindingException::class);
        $factory = new BindingFactory();
        $factory->create('foo');
    }

    public function test__detect_http_redirect()
    {
        $request = $this->createHttpRedirectRequest();

        $factory = new BindingFactory();

        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_REDIRECT, $factory->detectBindingType($request));
    }

    public function test__detect_http_post()
    {
        $request = $this->createHttpPostRequest();

        $factory = new BindingFactory();

        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_POST, $factory->detectBindingType($request));
    }

    public function test__detect_artifact_post()
    {
        $request = $this->createArtifactPostRequest();

        $factory = new BindingFactory();

        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_ARTIFACT, $factory->detectBindingType($request));
    }

    public function test__detect_artifact_get()
    {
        $request = $this->createArtifactGetRequest();

        $factory = new BindingFactory();

        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_ARTIFACT, $factory->detectBindingType($request));
    }

    public function test__detect_soap()
    {
        $request = $this->createSoapRequest();

        $factory = new BindingFactory();

        $this->assertEquals(SamlConstants::BINDING_SAML2_SOAP, $factory->detectBindingType($request));
    }

    public function test__detect_none_get()
    {
        $request = new Request();
        $request->setMethod('GET');

        $factory = new BindingFactory();

        $this->assertNull($factory->detectBindingType($request));
    }

    public function test__detect_none_post()
    {
        $request = new Request();
        $request->setMethod('POST');

        $factory = new BindingFactory();

        $this->assertNull($factory->detectBindingType($request));
    }

    public function test__get_binding_by_request_http_redirect()
    {
        $request = $this->createHttpRedirectRequest();
        $factory = new BindingFactory();
        $this->assertInstanceOf('LightSaml\Binding\HttpRedirectBinding', $factory->getBindingByRequest($request));
    }

    public function test__get_binding_by_request_http_post()
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
