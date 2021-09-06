<?php

namespace LSNepomuceno\LaravelAutentiqueV2;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Http;
use LSNepomuceno\LaravelAutentiqueV2\Exception\AutentiqueResponseException;
use LSNepomuceno\LaravelAutentiqueV2\Exception\AutentiqueTokenNotFoundException;

/**
 * Autentique
 * @link https://docs.autentique.com.br/api/
 * @version 2.0.0
 */
class Autentique extends Http
{
  /**
   * @var \Illuminate\Http\Client\PendingRequest
   */
  protected PendingRequest $client;

  /**
   * @var string
   */
  protected string $token, $postType, $isSandbox = 'false';

  /**
   * @var array
   */
  protected array $params, $signers;

  /**
   * @var string|null
   */
  protected ?string $file, $fileName;

  /**
   * @var bool
   */
  protected bool $deleteLocalFile = false;

  /**
   * @var string
   */
  const
    API_URL   = 'https://api.autentique.com.br/v2/graphql',
    JSON_TYPE = 'json',
    FORM_TYPE = 'form';

  /**
   * __construct
   *
   * @throws \LSNepomuceno\LaravelAutentiqueV2\Exception\AutentiqueTokenNotFoundException
   *
   * @return void
   */
  public function __construct()
  {
    $this->token = config('services.autentique.token');

    if (!$this->token) new AutentiqueTokenNotFoundException;

    $this->setClient();
  }

  /**
   * setIsSandbox - Defines whether the request will be of the test type
   *
   * @param  bool $isSandbox
   *
   * @return \LSNepomuceno\LaravelAutentiqueV2\Autentique
   */
  public function setIsSandbox(bool $isSandbox = true): Autentique
  {
    $this->isSandbox = var_export($isSandbox, true);
    return $this;
  }

  /**
   * setClient - Define client attrs
   *
   * @return void
   */
  private function setClient(): void
  {
    $this->client = self::baseUrl(self::API_URL)
      ->withToken($this->token);
  }

  /**
   * setPostType - Define post form type
   *
   * @param  string $type self::JSON_TYPE
   * @return \LSNepomuceno\LaravelAutentiqueV2\Autentique
   */
  public function setPostType(string $type = self::JSON_TYPE): Autentique
  {
    if (!in_array($type, [self::JSON_TYPE, self::FORM_TYPE])) {
      throw new \Exception("Error: Invalid type. Accept only: " . self::JSON_TYPE . ' or ' . self::FORM_TYPE . '.');
    }

    $this->postType = $type;

    return $this;
  }

  /**
   * setParams - Define post params
   *
   * @param  string $operationQuery
   * @param  array|string|null $variablesOrMaps null
   * @param  array|null $files null
   * @return \LSNepomuceno\LaravelAutentiqueV2\Autentique
   */
  public function setParams(
    string  $operationQuery,
    $variablesOrMaps = null,
    ?string $file = null,
    ?string $fileName = null,
    ?array $signers = null
  ): Autentique {
    switch ($this->postType) {
      case self::FORM_TYPE:
        $this->file = $file;
        $this->fileName = $fileName;
        $this->signers = $signers;
        $this->params = [
          'operation' => [
            'query'   => $operationQuery
          ]
        ];
        break;

      case self::JSON_TYPE:
      default:
        $this->params = [
          'query'     => $operationQuery,
          'variables' => $variablesOrMaps
        ];
        break;
    }

    return $this;
  }

  /**
   * setDeleteLocalFile - Define if local files has deleted
   *
   * @param  bool $deleteLocalFile true
   *
   * @return \LSNepomuceno\LaravelAutentiqueV2\Autentique
   */
  public function setDeleteLocalFile(bool $deleteLocalFile = true): Autentique
  {
    $this->deleteLocalFile = $deleteLocalFile;
    return $this;
  }

  /**
   * performPost - Standardizes the sending and receiving of requisitions
   *
   * @throws \LSNepomuceno\LaravelAutentiqueV2\Exception\AutentiqueResponseException
   *
   * @return \Illuminate\Support\Fluent
   */
  public function performPost(): Fluent
  {
    if ($this->postType === self::JSON_TYPE) {
      $response = $this->client->withBody(
        json_encode($this->params),
        'application/json'
      )->post('');

      $response = json_decode($response->body());
    }

    if ($this->postType === self::FORM_TYPE) {
      $dados = json_encode([
        'document' => ['name' => $this->fileName],
        'signers'  => $this->signers,
        'file'     => null
      ]);

      $this->params['operation'] = json_encode($this->params['operation']);

      $postFields = [
        'operations' => str_replace([',$variables"', '$sandbox'], ['","variables":' . $dados, $this->isSandbox], $this->params['operation']),
        'map' => '{"file": ["variables.file"]}',
        'file' => new \CURLFile($this->file),
      ];

      $curl = curl_init(self::API_URL);

      curl_setopt_array(
        $curl,
        [
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $postFields,
          CURLOPT_HTTPHEADER => ["Authorization: Bearer {$this->token}"]
        ]
      );

      $response = json_decode(curl_exec($curl));

      $responseError = json_last_error() !== JSON_ERROR_NONE ? 'Malformed json' : (curl_errno($curl) ? curl_error($curl) : null);

      curl_close($curl);

      /**
       * @throws AutentiqueResponseException
       */
      if ($responseError) throw new AutentiqueResponseException($responseError);
    }

    /**
     * @throws AutentiqueResponseException
     */
    if (isset($response->errors)) {
      $message = current(reset($response->errors));
      throw new AutentiqueResponseException($message);
    }

    if ($this->deleteLocalFile) File::delete($this->file);

    return new Fluent($response);
  }

  /**
   * runJson - Run default json post structure
   *
   * @param string $query
   * @param array $variables []
   * @return \Illuminate\Support\Fluent
   */
  public function runJson(string $query, array $variables = []): Fluent
  {
    return $this->setPostType(self::JSON_TYPE)
      ->setParams($query, $variables)
      ->performPost();
  }

  /**
   * errorMessages - Return Autentique error messages
   * @link https://docs.autentique.com.br/api/integracao/mensagens-de-erro
   *
   * @return array
   */
  private function errorMessages(): array
  {
    return [
      'unauthorized'       => 'Você não está mais autenticado',
      'invalid_date'       => 'Não é uma data válida',
      'document_not_found' => 'Documento não encontrado',
      'folder_not_found'   => 'Pasta não encontrada',
      'document_signed'    => 'O documento já foi assinado',
      'not_your_turn'      => 'Não é a sua vez de assinar o documento',
      'must_be_a_string'   => 'É somente permitido texto',
      'must_be_an_array'   => 'Não é uma lista',
      'not_a_valid_date'   => 'Não é uma data válida',
      'must_be_a_file'     => 'Não é um arquivo',
      'failed_to_upload'   => 'Erro ao enviar o arquivo',
      'field_required'     => 'Este campo é obrigatório',
      'must_be_at_least'   => 'Não pode ter menos que {{min}} caracteres',
      'format_is_invalid'  => 'O formato do campo está incorreto',
      'may_not_be_greater_than' => 'Não pode ter mais que {{max}} caracteres',
      'could_not_upload_file'   => 'Não foi possível enviar o arquivo',
      'unavailable_credits'     => 'Os seus créditos esgotaram',
      'without_permission'      => 'Você precisa ser um administrador da organização para executar esta ação.',
      'must_be_a_valid_file'    => 'Somente são permitidos arquivos com as extenções {{extensions}}',
      'must_be_a_valid_email_address' => 'Não é um email válido',
      'not_a_member_of_organization'  => 'Você precisa ser um membro da mesma organização para executar esta ação.'
    ];
  }
}
