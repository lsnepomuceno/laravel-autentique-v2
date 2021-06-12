<?php

namespace LSNepomuceno\LaravelAutentiqueV2;

use Illuminate\Support\Facades\File;

class MakeQuery
{
  /**
   * @var string
   */
  protected string $queryFile, $query;

  /**
   * setQueryFile - Define query file
   *
   * @param  string $file
   * @param  string $dir
   * @return \LSNepomuceno\LaravelAutentiqueV2\MakeQuery
   */
  public function setQueryFile(string $file, string $dir): MakeQuery
  {
    $baseDir = str_replace('\\', '/', __DIR__);

    $file = "{$baseDir}/GraphQLQueries/{$dir}/{$file}.graphql";

    if (!File::exists($file)) {
      throw new \Exception("Error: Query file not found.");
    }

    $this->queryFile = $file;

    return $this;
  }

  /**
   * makeQuery - Format query removing break lines
   *
   * @return string
   */
  public function makeQuery(): string
  {
    $this->query = File::get($this->queryFile);

    // remove all break lines
    $this->query = preg_replace("/[\n\r]/", '', $this->query);

    return $this->query;
  }
}
