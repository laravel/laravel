<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Psy\Readline\Hoa;

/**
 * The `hoa://Library/` node.
 */
class ProtocolNodeLibrary extends ProtocolNode
{
    /**
     * Queue of the component.
     */
    public function reach(?string $queue = null)
    {
        $withComposer = \class_exists('Composer\Autoload\ClassLoader', false) ||
            ('cli' === \PHP_SAPI && \file_exists(__DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'autoload.php'));

        if ($withComposer) {
            return parent::reach($queue);
        }

        if (!empty($queue)) {
            $head = $queue;

            if (false !== $pos = \strpos($queue, '/')) {
                $head = \substr($head, 0, $pos);
                $queue = \DIRECTORY_SEPARATOR.\substr($queue, $pos + 1);
            } else {
                $queue = null;
            }

            $out = [];

            foreach (\explode(';', $this->_reach) as $part) {
                $out[] = "\r".$part.\strtolower($head).$queue;
            }

            $out[] = "\r".\dirname(__DIR__, 5).$queue;

            return \implode(';', $out);
        }

        $out = [];

        foreach (\explode(';', $this->_reach) as $part) {
            $pos = \strrpos(\rtrim($part, \DIRECTORY_SEPARATOR), \DIRECTORY_SEPARATOR) + 1;
            $head = \substr($part, 0, $pos);
            $tail = \substr($part, $pos);
            $out[] = $head.\strtolower($tail);
        }

        $this->_reach = \implode(';', $out);

        return parent::reach($queue);
    }
}
