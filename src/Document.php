<?php

namespace LSNepomuceno\LaravelAutentiqueV2;

use Illuminate\Support\Fluent;

class Document
{
  /**
   * @var string
   */
  private const DIR = 'documents';

  /**
   * @var \LSNepomuceno\LaravelAutentiqueV2\Autentique
   */
  private Autentique $autentique;

  /**
   * @var \LSNepomuceno\LaravelAutentiqueV2\MakeQuery
   */
  private MakeQuery $makeQuery;

  /**
   * __construct
   *
   * @param bool $isSandbox - Defines whether the request will be of the test type
   *
   * @return void
   */
  public function __construct(bool $isSandbox = false)
  {
    $this->autentique = new Autentique();
    $this->autentique->setIsSandbox($isSandbox);
    $this->makeQuery  = new MakeQuery(self::DIR);
  }

  /**
   * changeFolder - Change document folder location
   * @link https://docs.autentique.com.br/api/integracao/movendo-documento-para-pasta
   *
   * @param string $documentId
   * @param string $destinationFolderId
   *
   * @return \Illuminate\Support\Fluent
   */
  public function changeFolder(string $documentId, string $destinationFolderId): Fluent
  {
    $query = $this->makeQuery
      ->setQueryFile(__METHOD__)
      ->makeQuery(
        ['$documentId', '$destinationFolderId'],
        [$documentId, $destinationFolderId]
      );

    return $this->autentique->runJson($query);
  }

  /**
   * create - Create a new document
   * @link https://docs.autentique.com.br/api/integracao/criando-um-documento
   *
   * @param string $documentName
   * @param string $documentPath
   * @param bool $deleteLocalAfterProcess
   *
   * @return \Illuminate\Support\Fluent
   */
  public function create(string $documentName, string $documentPath, bool $deleteLocalAfterProcess = true): Fluent
  {
    $query = $this->makeQuery->setQueryFile(__METHOD__)->makeQuery();
    $maps  = '{"file": ["variables.file"]}';

    return $this->autentique
      ->setPostType($this->autentique::FORM_TYPE)
      ->setParams($query, $maps, $documentPath)
      ->performPost();
  }

  /**
   * delete - Delete a specific document
   *
   * @param string $documentId
   *
   * @return \Illuminate\Support\Fluent
   */
  public function delete(string $documentId): Fluent
  {
    $query = $this->makeQuery
      ->setQueryFile(__METHOD__)
      ->makeQuery(['$documentId'], [$documentId]);

    return $this->autentique->runJson($query);
  }

  /**
   * read - Retrieve a specific document
   * @link https://docs.autentique.com.br/api/integracao/resgatando-documentos#resgatando-um-documento-especifico
   *
   * @param string $documentId
   *
   * @return \Illuminate\Support\Fluent
   */
  public function read(string $documentId): FLuent
  {
    $query = $this->makeQuery
      ->setQueryFile(__METHOD__)
      ->makeQuery(['$documentId'], [$documentId]);

    return $this->autentique->runJson($query);
  }

  /**
   * readAll - Retrieve all documents definning per page and pagination, does not receive documents from directories
   * @link https://docs.autentique.com.br/api/integracao/resgatando-documentos#listando-documentos
   *
   * @param int $perPage 60
   * @param int $page 1
   *
   * @return \Illuminate\Support\Fluent
   */
  public function readAll(int $perPage = 60, int $page = 1): Fluent
  {
    $query = $this->makeQuery
      ->setQueryFile(__METHOD__)
      ->makeQuery(['$perPage', '$page'], [$perPage, $page]);

    return $this->autentique->runJson($query);
  }

  /**
   * sign - Sign a specific document
   * @link https://docs.autentique.com.br/api/integracao/assinando-um-documento
   *
   * @param string $documentId
   *
   * @return \Illuminate\Support\Fluent
   */
  public function sign(string $documentId): Fluent
  {
    $query = $this->makeQuery
      ->setQueryFile(__METHOD__)
      ->makeQuery(['$documentId'], [$documentId]);

    return $this->autentique->runJson($query);
  }
}
