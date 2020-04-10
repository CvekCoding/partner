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

namespace Cvek\PartnerBundle\Messenger\Handler;

use Cvek\PartnerBundle\Messenger\Message\PartnerAlreadyExistsErrorMessage;
use Cvek\PartnerBundle\Messenger\Message\PartnerCreatedMessage;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PartnerCreatedHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private MessageBusInterface $eventBus;
    private string $partnerClass;
    private bool $useHandler;

    public function __construct(EntityManagerInterface $entityManager,
                                MessageBusInterface $eventBus,
                                string $partnerClass,
                                bool $useHandler)
    {
        $this->em = $entityManager;
        $this->eventBus = $eventBus;
        $this->partnerClass = $partnerClass;
        $this->useHandler = $useHandler;
    }

    public function __invoke(PartnerCreatedMessage $message)
    {
        if (!$this->useHandler) {
            return;
        }

        if (null !== $this->em->find($this->partnerClass, $message->getPartnerId())) {
            $this->eventBus->dispatch(new PartnerAlreadyExistsErrorMessage($message->getPartnerId()));

            return;
        }

        $partner = (new $this->partnerClass(Uuid::fromString($message->getPartnerId())));

        if (\method_exists($partner, 'setCurrency')) {
            $partner->setCurrency($message->getCurrency());
        }

        $this->em->persist($partner);
        $this->em->flush();
    }
}
