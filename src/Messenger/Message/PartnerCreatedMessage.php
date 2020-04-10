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

namespace Cvek\PartnerBundle\Messenger\Message;

use Symfony\Component\Validator\Constraints as Assert;

final class PartnerCreatedMessage
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="Partner Id can not be blank")
     * @Assert\Uuid(message="Partner Id must be valid UUID")
     */
    private string $partnerId;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Currency can not be blank")
     * @Assert\Currency(message="Currency is not valid")
     */
    private string $currency;

    public function __construct(string $partnerId, string $currency)
    {
        $this->partnerId = $partnerId;
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getPartnerId(): string
    {
        return $this->partnerId;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
}
