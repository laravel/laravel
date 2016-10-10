<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Catalogue;

/**
 * Diff operation between two catalogues.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class DiffOperation extends AbstractOperation
{
    /**
     * {@inheritdoc}
     */
    protected function processDomain($domain)
    {
        $this->messages[$domain] = array(
            'all'      => array(),
            'new'      => array(),
            'obsolete' => array(),
        );

        foreach ($this->source->all($domain) as $id => $message) {
            if ($this->target->has($id, $domain)) {
                $this->messages[$domain]['all'][$id] = $message;
                $this->result->add(array($id => $message), $domain);
            } else {
                $this->messages[$domain]['obsolete'][$id] = $message;
            }
        }

        foreach ($this->target->all($domain) as $id => $message) {
            if (!$this->source->has($id, $domain)) {
                $this->messages[$domain]['all'][$id] = $message;
                $this->messages[$domain]['new'][$id] = $message;
                $this->result->add(array($id => $message), $domain);
            }
        }
    }
}
