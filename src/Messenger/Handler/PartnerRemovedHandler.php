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

use Cvek\PartnerBundle\Messenger\Message\PartnerRemovedMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PartnerRemovedHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private string $partnerClass;
    private bool $useHandler;

    public function __construct(EntityManagerInterface $entityManager, string $partnerClass, bool $useHandler)
    {
        $this->em = $entityManager;
        $this->partnerClass = $partnerClass;
        $this->useHandler = $useHandler;
    }

    public function __invoke(PartnerRemovedMessage $message)
    {
        if (!$this->useHandler) {
            return;
        }

        if (null === $partner = $this->em->find($this->partnerClass, $message->getPartnerId())) {
            return;
        }

        $this->em->remove($partner);
        $this->em->flush();
    }
}
