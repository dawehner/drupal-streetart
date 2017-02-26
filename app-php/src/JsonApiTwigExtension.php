<?php

namespace AppPhp;

use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;

class JsonApiTwigExtension extends \Twig_Extension {

  /**
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * @var string
   */
  protected $backendHost;

  /**
   * @var \GuzzleHttp\Psr7\Request
   */
  protected $request;

  public function __construct(Client $client, $backend_host, RequestInterface $request) {
    $this->client = $client;
    $this->backendHost = $backend_host;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'jsonapi';
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('api', [$this, 'fetchFromJsonApi']),
      new \Twig_SimpleFunction('jsonapi', [$this, 'fetchFromJsonApi']),
      new \Twig_SimpleFunction('filter_field_by_value', [$this, 'filterFieldByValue']),
      new \Twig_SimpleFunction('baseHost', [$this, 'baseHost']),
    ];
  }

  public function baseHost() {
    return $this->backendHost;
  }

  public function fetchFromJsonApi($resource, array $options = []) {
    $options += ['query' => []];
    $options['query'] += ['_format' => 'api_json'];
    $response = $this->client->get($this->backendHost . '/jsonapi/' . ltrim($resource, '/'), $options);

    return json_decode((string) $response->getBody(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
  }

  public function filterFieldByValue(array $data, $field, $value) {
    $result = array_filter($data, function ($single_entry) use ($field, $value) {
      $single_entry = (array) $single_entry;
      return $single_entry[$field] == $value;
    });
    if ($result) {
      return reset($result)['attributes'];
    }
  }

}
