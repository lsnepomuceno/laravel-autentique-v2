<?php

namespace LSNepomuceno\LaravelAutentiqueV2;

use Illuminate\Support\Fluent;

class Folder
{
  /**
   * @var string
   */
  private const DIR = 'folders';

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
   * @return void
   */
  public function __construct()
  {
    $this->autentique = new Autentique;
    $this->makeQuery  = new MakeQuery(self::DIR);
  }

  /**
   * create - Create a new folder
   * @link https://docs.autentique.com.br/api/integracao/criando-pastas#criando-uma-pasta-normal
   *
   * @param string $folderName
   *
   * @return \Illuminate\Support\Fluent
   */
  public function create(string $folderName): Fluent
  {
    $query = $this->makeQuery->setQueryFile(__METHOD__)->makeQuery();
    $variables =  [
      'folder' => [
        'name' => $folderName
      ]
    ];

    return $this->autentique->runJson($query, $variables);
  }

  /**
   * delete - Deletes a directory and all of its documents
   * @param string $folderId
   *
   * @return \Illuminate\Support\Fluent
   */
  public function delete(string $folderId): Fluent
  {
    $query = $this->makeQuery
      ->setQueryFile(__METHOD__)
      ->makeQuery(['$folderId'], [$folderId]);

    return $this->autentique->runJson($query);
  }

  /**
   * documentsByFolder - Retrieve all documents by folder
   * @param string $folderId
   * @param int    $perPage 60
   * @param int    $page 1
   *
   * @return \Illuminate\Support\Fluent
   */
  public function documentsByFolder(string $folderId, int $perPage = 60, int $page = 1): Fluent
  {
    $query = $this->makeQuery
      ->setQueryFile(__METHOD__)
      ->makeQuery(
        ['$folderId', '$perPage', '$page'],
        [$folderId, $perPage, $page]
      );

    return $this->autentique->runJson($query);
  }

  /**
   * read - Retrieve a folder data
   * @param string $folderId
   *
   * @return \Illuminate\Support\Fluent
   */
  public function read(string $folderId): Fluent
  {
    $query = $this->makeQuery
      ->setQueryFile(__METHOD__)
      ->makeQuery(['$folderId'], [$folderId]);

    return $this->autentique->runJson($query);
  }

  /**
   * readAll - Recover all data from folders
   *
   * @return \Illuminate\Support\Fluent
   */
  public function readAll(): Fluent
  {
    $query = $this->makeQuery->setQueryFile(__METHOD__)->makeQuery();

    return $this->autentique->runJson($query);
  }
}
