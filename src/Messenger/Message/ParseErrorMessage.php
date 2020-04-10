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

final class ParseErrorMessage
{
    private string $error;
    private string $source;

    public function __construct(string $error, string $source)
    {
        $this->error = $error;
        $this->source = $source;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
