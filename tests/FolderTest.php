<?php

namespace LSNepomuceno\LaravelAutentiqueV2\Tests;

use LSNepomuceno\LaravelAutentiqueV2\{Folder};
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Fluent;
use LSNepomuceno\LaravelAutentiqueV2\Exception\AutentiqueResponseException;

class FolderTest extends TestCase
{
  private function createFolder()
  {
    try {
      return (new Folder)->create('folder-test');
    } catch (AutentiqueResponseException $th) {
      throw $th;
    }
  }

  private function deleteFolder(string $folderId)
  {
    try {
      return (new Folder)->delete($folderId);
    } catch (AutentiqueResponseException $th) {
      throw $th;
    }
  }

  public function testValidateCreationAndDeletionResponses()
  {
    $createdResponse = $this->createFolder();

    $this->assertInstanceOf(Fluent::class, $createdResponse);
    $this->assertObjectHasAttribute('createFolder', $createdResponse->data);
    $this->assertObjectHasAttribute('id', $createdResponse->data->createFolder);

    $deleteResponse = $this->deleteFolder($createdResponse->data->createFolder->id);

    $this->assertInstanceOf(Fluent::class, $deleteResponse);
    $this->assertObjectHasAttribute('deleteFolder', $deleteResponse->data);
    $this->assertTrue($deleteResponse->data->deleteFolder);
  }

  public function testValidateDocumentsByFolder()
  {
    $createdResponse = $this->createFolder();

    $documentsByFolderResponse = (new Folder)->documentsByFolder(
      $createdResponse->data->createFolder->id
    );

    $this->assertInstanceOf(Fluent::class, $documentsByFolderResponse);
    $this->assertObjectHasAttribute('documentsByFolder', $documentsByFolderResponse->data);

    $this->deleteFolder($createdResponse->data->createFolder->id);
  }

  public function testValidateReadAllResponse()
  {
    try {
      $folders = (new Folder)->readAll();
    } catch (AutentiqueResponseException $th) {
      throw $th;
    }

    $this->assertInstanceOf(Fluent::class, $folders);
    $this->assertObjectHasAttribute('folders', $folders->data);
  }
}
