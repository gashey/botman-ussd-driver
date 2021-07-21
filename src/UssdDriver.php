<?php

namespace Gashey\BotmanUssdDriver;

use Illuminate\Support\Collection;
use BotMan\BotMan\Drivers\HttpDriver;
use BotMan\BotMan\Interfaces\WebAccess;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Users\User;
use BotMan\BotMan\Messages\Outgoing\Question;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Gashey\BotmanUssdDriver\Lib\UssdRequest;
use Gashey\BotmanUssdDriver\Lib\UssdResponse;

class UssdDriver extends HttpDriver
{
    const DRIVER_NAME = 'Ussd';

    /** @var OutgoingMessage[] */
    protected $replies = [];

    /** @var int */
    protected $replyStatusCode = 200;

    /** @var string */
    protected $errorMessage = '';

    /** @var array */
    protected $messages = [];

    /**
     * @param Request $request
     */
    public function buildPayload(Request $request)
    {
        $this->payload = new ParameterBag((array) $request->request->all());
        $this->event = Collection::make((array) $this->payload->all());
        $this->content = $request->getContent();
        $this->config = Collection::make($this->config->get('ussd', []));
    }

    /**
     * @return bool
     */
    public function matchesRequest()
    {
        return !is_null($this->event->get('ussdServiceOp'))
            || !is_null($this->event->get('ussdString'))
            || !is_null($this->event->get('sessionID'))
            || !is_null($this->event->get('network'))
            || !is_null($this->event->get('msisdn'));
    }

    /**
     * Retrieve the chat message.
     *
     * @return array
     */
    public function getMessages()
    {
        if (empty($this->messages)) {
            $message = $this->event->get('ussdServiceOp') == UssdRequest::RELEASE ?
                $this->config->get("cancel_text", "stop") : $this->event->get('ussdString');
            $userId = $this->event->get('msisdn');
            $sessionId = $this->event->get('sessionID');
            $this->messages = [new IncomingMessage($message, $sessionId, $userId, $this->payload)];
        }
        return $this->messages;
    }

    /**
     * Retrieve User information.
     * @param IncomingMessage $matchingMessage
     * @return UserInterface
     */
    public function getUser(IncomingMessage $matchingMessage)
    {
        $networks = $this->config->get('network_mapping');
        $user_info = [
            'msisdn' => $matchingMessage->getPayload()->get('msisdn'),
            'network' => $networks[$matchingMessage->getPayload()->get('network')],
        ];
        return new User($matchingMessage->getSender(), null, null, null, $user_info);
    }

    /**
     * @param IncomingMessage $message
     * @return \BotMan\BotMan\Messages\Incoming\Answer
     */
    public function getConversationAnswer(IncomingMessage $message)
    {
        return Answer::create($message->getText())
            ->setValue($message->getText())
            ->setMessage($message)
            ->setInteractiveReply(true);
    }

    /**
     * @param string|Question|OutgoingMessage $message
     * @param IncomingMessage $matchingMessage
     * @param array $additionalParameters
     * @return Response
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = [])
    {
        if (!$message instanceof WebAccess && !$message instanceof OutgoingMessage) {
            $this->errorMessage = 'Unsupported message type.';
            $this->replyStatusCode = 500;
        }

        return [
            'message' => $message,
            'sessionID' => $matchingMessage->getPayload()->get('sessionID'),
            'ussdServiceOp' => empty($additionalParameters) ? UssdResponse::RESPONSE : head($additionalParameters)
        ];
    }

    /**
     * @param mixed $payload
     * @return Response
     */
    public function sendPayload($payload)
    {
        $this->replies[] = $payload;
    }

    /**
     * @param $messages
     * @return array
     */
    protected function buildReply($messages)
    {
        $replyData = Collection::make($messages)->transform(function ($replyData) {
            $reply = [];
            $message = $replyData['message'];

            if ($message instanceof OutgoingMessage) {
                $reply = [
                    'message' => $message->getText(),
                    'sessionID' => $replyData['sessionID'],
                    'ussdServiceOp' => $replyData['ussdServiceOp'],

                ];
            }

            return $reply;
        })->toArray();

        return $replyData;
    }

    /**
     * Send out message response.
     */
    public function messagesHandled()
    {
        $messages = $this->buildReply($this->replies);

        // Reset replies
        $this->replies = [];

        Response::create(json_encode(last($messages)), $this->replyStatusCode, [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Allow-Origin' => '*',
        ])->send();
    }

    /**
     * Low-level method to perform driver specific API requests.
     *
     * @param string $endpoint
     * @param array $parameters
     * @param \BotMan\BotMan\Messages\Incoming\IncomingMessage $matchingMessage
     * @return Response
     */
    public function sendRequest($endpoint, array $parameters, IncomingMessage $matchingMessage)
    {
        return new Exception("Error Processing Request", 1);
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->config->get('network_mapping')) && !empty($this->config->get('cancel_text'));
    }
}
