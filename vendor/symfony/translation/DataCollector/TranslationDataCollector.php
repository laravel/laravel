<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 *
 * @final
 */
class TranslationDataCollector extends DataCollector implements LateDataCollectorInterface
{
    public function __construct(
        private DataCollectorTranslator $translator,
    ) {
    }

    public function lateCollect(): void
    {
        $messages = $this->sanitizeCollectedMessages($this->translator->getCollectedMessages());

        $this->data += $this->computeCount($messages);
        $this->data['messages'] = $messages;

        $this->data = $this->cloneVar($this->data);
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $this->data['locale'] = $this->translator->getLocale();
        $this->data['fallback_locales'] = $this->translator->getFallbackLocales();
        $this->data['global_parameters'] = $this->translator->getGlobalParameters();
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getMessages(): array|Data
    {
        return $this->data['messages'] ?? [];
    }

    public function getCountMissings(): int
    {
        return $this->data[DataCollectorTranslator::MESSAGE_MISSING] ?? 0;
    }

    public function getCountFallbacks(): int
    {
        return $this->data[DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK] ?? 0;
    }

    public function getCountDefines(): int
    {
        return $this->data[DataCollectorTranslator::MESSAGE_DEFINED] ?? 0;
    }

    public function getLocale(): ?string
    {
        return !empty($this->data['locale']) ? $this->data['locale'] : null;
    }

    /**
     * @internal
     */
    public function getFallbackLocales(): Data|array
    {
        return (isset($this->data['fallback_locales']) && \count($this->data['fallback_locales']) > 0) ? $this->data['fallback_locales'] : [];
    }

    /**
     * @internal
     */
    public function getGlobalParameters(): Data|array
    {
        return $this->data['global_parameters'] ?? [];
    }

    public function getName(): string
    {
        return 'translation';
    }

    private function sanitizeCollectedMessages(array $messages): array
    {
        $result = [];
        foreach ($messages as $key => $message) {
            $messageId = $message['locale'].$message['domain'].$message['id'];

            if (!isset($result[$messageId])) {
                $message['count'] = 1;
                $message['parameters'] = !empty($message['parameters']) ? [$message['parameters']] : [];
                $messages[$key]['translation'] = $this->sanitizeString($message['translation']);
                $result[$messageId] = $message;
            } else {
                if (!empty($message['parameters'])) {
                    $result[$messageId]['parameters'][] = $message['parameters'];
                }

                ++$result[$messageId]['count'];
            }

            unset($messages[$key]);
        }

        return $result;
    }

    private function computeCount(array $messages): array
    {
        $count = [
            DataCollectorTranslator::MESSAGE_DEFINED => 0,
            DataCollectorTranslator::MESSAGE_MISSING => 0,
            DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK => 0,
        ];

        foreach ($messages as $message) {
            ++$count[$message['state']];
        }

        return $count;
    }

    private function sanitizeString(string $string, int $length = 80): string
    {
        $string = trim(preg_replace('/\s+/', ' ', $string));

        if (false !== $encoding = mb_detect_encoding($string, null, true)) {
            if (mb_strlen($string, $encoding) > $length) {
                return mb_substr($string, 0, $length - 3, $encoding).'...';
            }
        } elseif (\strlen($string) > $length) {
            return substr($string, 0, $length - 3).'...';
        }

        return $string;
    }
}
