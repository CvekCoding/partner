<?php
/*
 * This file is part of the Aqua Delivery package.
 *
 * (c) Sergey Logachev <svlogachev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cvek\PartnerBundle\Messenger\Serializer;

use Cvek\PartnerBundle\Messenger\Message\PartnerCreatedMessage;
use Cvek\PartnerBundle\Messenger\Message\ParseErrorMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface as MessengerSerializer;
use Symfony\Component\Serializer\SerializerInterface;

final class PartnerCreatedSerializer implements MessengerSerializer
{
    private SerializerInterface $serializer;
    private MessageBusInterface $eventBus;

    public function __construct(SerializerInterface $serializer, MessageBusInterface $eventBus)
    {
        $this->serializer = $serializer;
        $this->eventBus = $eventBus;
    }

    /**
     * @inheritDoc
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        try {
            $event = $this->serializer->deserialize($encodedEnvelope['body'], PartnerCreatedMessage::class, 'json');
        } catch (\Exception $e) {
            $error = 'Can not decode income message. Error: ' . $e->getMessage();
            $this->eventBus->dispatch(new ParseErrorMessage($error, $encodedEnvelope['body']));

            throw new MessageDecodingFailedException($error);
        }

        return new Envelope($event);
    }

    /**
     * @inheritDoc
     */
    public function encode(Envelope $envelope): array
    {
        throw new \LogicException('Partner register message can not be published');
    }
}
