<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Protocol;

/**
 * Interface that defines a customizable protocol processor that serializes
 * Redis commands and parses replies returned by the server to PHP objects
 * using a pluggable set of classes defining the underlying wire protocol.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ComposableProtocolInterface extends ProtocolInterface
{
    /**
     * Sets the command serializer to be used by the protocol processor.
     *
     * @param CommandSerializerInterface $serializer Command serializer.
     */
    public function setSerializer(CommandSerializerInterface $serializer);

    /**
     * Returns the command serializer used by the protocol processor.
     *
     * @return CommandSerializerInterface
     */
    public function getSerializer();

    /**
     * Sets the response reader to be used by the protocol processor.
     *
     * @param ResponseReaderInterface $reader Response reader.
     */
    public function setReader(ResponseReaderInterface $reader);

    /**
     * Returns the response reader used by the protocol processor.
     *
     * @return ResponseReaderInterface
     */
    public function getReader();
}
