<?php

namespace AntiCaptcha;

use AntiCaptcha\Task\AbstractTask;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\AbstractLogger;
use AntiCaptcha\Service\AbstractService;
use AntiCaptcha\Exception\AntiCaptchaException;
use AntiCaptcha\Exception\InvalidAntiCaptchaServiceException;

/**
 * Class AntiCaptcha
 * @package AntiCaptcha
 */
class AntiCaptcha
{
    /** @var AbstractService $service */
    protected $service;

    /** @var GuzzleClient $client */
    protected $client;

    /** @var AbstractLogger $logger */
    protected $logger;

    /** @var bool $debugMod */
    protected $debugMod = false;

    /** @var array */
    protected $options = [
        'timeout_start' => 6,

        // time between requesting of captcha solving
        'timeout_ready' => 3,

        // timeout of captcha solving
        'timeout_max' => 300,
    ];

    /**
     * Constants list
     */
    const SERVICE_ANTICAPTCHA = 'AntiCaptcha';

    /**
     * Captcha service list
     *
     * @var array
     */
    protected static $serviceMap = [
        self::SERVICE_ANTICAPTCHA,
    ];

    /**
     * AntiCaptcha constructor.
     * @param string|AbstractService $service
     * @param array $options
     *
     * @throws AntiCaptchaException
     * @throws InvalidAntiCaptchaServiceException
     */
    public function __construct($service, array $options = [])
    {
        if (is_string($service)) {
            if (false === in_array($service, self::$serviceMap)) {
                throw new InvalidAntiCaptchaServiceException($service);
            }

            $serviceNamespace = '\\AntiCaptcha\\Service\\' . $service;
            $service = new $serviceNamespace;
        }

        if ($service) {
            $this->setService($service);

            if (!empty($options['api_key'])) {
                $this->getService()->setApiKey($options['api_key']);
                unset($options['api_key']);
            }
        }

        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }

        if (!empty($options['debug'])) {
            $this->debugMod = true;
            $this->setLogger(new Logger);
        }

        // set Http Client
        $this->setClient(new GuzzleClient);
    }


    /**
     * Setup Anti captcha service provider.
     * @param AbstractService $service
     *
     * @return $this
     */
    public function setService(AbstractService $service)
    {
        $this->service = $service;

        return $this;
    }


    /**
     * Method getService description.
     *
     * @return AbstractService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set your custom Logger
     * @param AbstractLogger $logger
     *
     * @return AntiCaptcha
     */
    public function setLogger(AbstractLogger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * HttpClient.
     * @param $client
     *
     * @return AntiCaptcha
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @param $debugString
     */
    protected function debug($debugString)
    {
        if ($this->debugMod && $this->logger) {
            $this->logger->debug($debugString);
        }
    }

    /**
     * Method balance description.
     *
     * @return mixed
     * @throws AntiCaptchaException
     */
    public function balance()
    {
        $this->debug('check balance ...');
        $response = $this->sendRequest('/getBalance');

        return $response['balance'];
    }


    /**
     * Method to recognize image request.
     *
     * @param $image
     * @param ?string $url
     * @param array $params
     * @param string $languagePool
     *
     * @return ?string
     * @throws AntiCaptchaException
     */
    public function recognizeImage($image, $url = null, $params = [], $languagePool = 'en')
    {
        if (null !== $url) {
            $request = $this->client->request('GET', $url);
            $image = $request->getBody();
        }

        $requestParams = [
            'task' => array_merge([
                'type' => 'ImageToTextTask',
                'body' => base64_encode($image),
                'phrase' => false,
                'case' => false,
                'numeric' => 0,
                'math' => false,
                'minLength' => 0,
                'maxLength' => 0
            ], $params),
            'languagePool' => $languagePool
        ];

        $body = $this->sendRequest('/createTask', $requestParams);

        $taskId = $body['taskId'];

        if (empty($taskId)) {
            throw new AntiCaptchaException('Did not created task');
        }

        $captchaResponse = $this->getResult($taskId);

        return $captchaResponse['solution']['text'];
    }

    /**
     * @param AbstractTask $task
     * @return mixed
     *
     * @throws AntiCaptchaException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function recognizeTask(AbstractTask $task)
    {
        $requestParams = [
            'task' => $task->getTaskParams()
        ];

        $requestParams = array_merge($requestParams, $task->otherRequestParams());

        $body = $this->sendRequest('/createTask', $requestParams);
        $taskId = $body['taskId'];

        if (empty($taskId)) {
            throw new AntiCaptchaException('Did not created task');
        }

        $captchaResponse = $this->getResult($taskId);

        return $captchaResponse['solution'];
    }


    /**
     * Method getResult description.
     * @param $taskId
     *
     * @return array
     * @throws AntiCaptchaException
     */
    protected function getResult($taskId)
    {
        $this->debug('captcha sent, got task ID: ' . $taskId);

        // Delay, before first captcha check
        $this->debug('waiting for ' . $this->options['timeout_start'] .  ' seconds');
        sleep($this->options['timeout_start']);

        $waitTime = 0;

        while (true) {
            $response = $this->sendRequest('/getTaskResult', [
                'taskId' => $taskId,
            ]);

            if ($response['status'] === 'ready') {
                return $response;

            }

            $this->debug('captcha is not ready yet');

            $waitTime += $this->options['timeout_ready'];

            if ($waitTime > $this->options['timeout_max']) {
                $this->debug('timelimit (' . $this->options['timeout_max'] . ') hit');
                throw new AntiCaptchaException('Timeout of resolving captcha');
            }

            $this->debug('waiting for ' . $this->options['timeout_ready'] . ' seconds');
            sleep($this->options['timeout_ready']);
        }
    }


    /**
     * @param string $action
     * @param array $requestParams
     * @return array
     *
     * @throws AntiCaptchaException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequest($action, $requestParams = [])
    {
        $url = $this->getService()->getApiUrl() . $action;

        $this->debug('request to: ' . $url);

        $result = $this->client->post($url, [
            'body' => json_encode(array_merge($this->getBasicParams(), $requestParams)),
            'headers'  => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->debug('response with status: ' . $result->getStatusCode());
        $responseBody = $result->getBody()->getContents();
        $this->debug('response with body: ' . $responseBody);

        return $this->resolveResponse($responseBody);
    }

    /**
     * @return array
     */
    protected function getBasicParams(): array
    {
        return array_merge([
            'clientKey' => $this->getService()->getApiKey(),
        ], $this->getService()->getParams());
    }


    /**
     * @param ResponseInterface $response
     * @return array
     *
     * @throws AntiCaptchaException
     */
    protected function resolveResponse($body)
    {
        $data = json_decode($body, true);

        if ($data['errorId'] !== 0) {
            throw new AntiCaptchaException($data['errorDescription'], $data['errorCode']);
        }

        return $data;
    }
}
