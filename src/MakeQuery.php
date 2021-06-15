<?php

namespace LSNepomuceno\LaravelAutentiqueV2;

use Illuminate\Support\Facades\File;

class MakeQuery
{
  /**
   * @var string
   */
  protected string $queryFile, $query, $dir;

  /**
   * __construct
   *
   * @param  string $dir
   * @return void
   */
  public function __construct(string $dir)
  {
    $this->dir = $dir;
  }

  /**
   * setQueryFile - Define query file
   *
   * @param  string $file
   *
   * @throws \ReflectionException
   *
   * @return \LSNepomuceno\LaravelAutentiqueV2\MakeQuery
   */
  public function setQueryFile(string $file): MakeQuery
  {
    try {
      $file = (new \ReflectionMethod($file))->getName();
      $baseDir = str_replace('\\', '/', __DIR__);
    } catch (\ReflectionException $th) {
      throw $th;
    }

    $file = "{$baseDir}/GraphQLQueries/{$this->dir}/{$file}.graphql";
    if (!File::exists($file)) {
      throw new \Exception("Error: Query file not found.");
    }

    $this->queryFile = $file;

    return $this;
  }

  /**
   * makeQuery - Format query removing break lines
   *
   * @param  array|null $search
   * @param  array|null $replace
   * @return string
   */
  public function makeQuery(?array $search = null, ?array $replace = null): string
  {
    $this->query = File::get($this->queryFile);

    // remove all break lines
    $this->query = preg_replace("/[\n\r]/", '', $this->query);

    if ($search && $replace) {
      $this->query = str_replace($search, $replace, $this->query);
    }

    return $this->query;
  }
}
