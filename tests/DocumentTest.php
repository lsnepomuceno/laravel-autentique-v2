<?php

namespace LSNepomuceno\LaravelAutentiqueV2\Tests;

use LSNepomuceno\LaravelAutentiqueV2\Document;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Fluent;
use LSNepomuceno\LaravelAutentiqueV2\Exception\AutentiqueResponseException;

class DocumentTest extends TestCase
{
  private function createDocument()
  {
    try {
      return (new Document(true))->create('DOCUMENTO TESTE', realpath('teste.pdf'), false);
    } catch (AutentiqueResponseException $th) {
      throw $th;
    }
  }

  public function testValidateDocumentCreation()
  {
    $createResponse = $this->createDocument();
    $this->assertInstanceOf(Fluent::class, $createResponse);

    dd($createResponse);
  }
}
