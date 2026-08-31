<?php

namespace Illuminate\Mail\Transport;

use Aws\Exception\AwsException;
use Aws\SesV2\SesV2Client;
use Illuminate\Support\Collection;
use Stringable;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Header\MetadataHeader;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Message;

class SesV2Transport extends AbstractTransport implements Stringable
{
    /**
     * The Amazon SES V2 instance.
     *
     * @var \Aws\SesV2\SesV2Client
     */
    protected $ses;

    /**
     * The Amazon SES transmission options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Create a new SES V2 transport instance.
     *
     * @param  \Aws\SesV2\SesV2Client  $ses
     * @param  array  $options
     */
    public function __construct(SesV2Client $ses, $options = [])
    {
        $this->ses = $ses;
        $this->options = $options;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {
        $options = $this->options;

        if ($message->getOriginalMessage() instanceof Message) {
            if ($listManagementOptions = $this->listManagementOptions($message)) {
                $options['ListManagementOptions'] = $listManagementOptions;
            }

            foreach ($message->getOriginalMessage()->getHeaders()->all() as $header) {
                if ($header instanceof MetadataHeader) {
                    $options['EmailTags'][] = ['Name' => $header->getKey(), 'Value' => $header->getValue()];
                }
            }
        }

        try {
            $result = $this->ses->sendEmail(
                array_merge(
                    $options, [
                        'Source' => $message->getEnvelope()->getSender()->toString(),
                        'Destination' => [
                            'ToAddresses' => (new Collection($message->getEnvelope()->getRecipients()))
                                ->map
                                ->toString()
                                ->values()
                                ->all(),
                        ],
                        'Content' => [
                            'Raw' => [
                                'Data' => $message->toString(),
                            ],
                        ],
                    ]
                )
            );
        } catch (AwsException $e) {
            $reason = $e->getAwsErrorMessage() ?? $e->getMessage();

            throw new TransportException(
                sprintf('Request to AWS SES V2 API failed. Reason: %s.', $reason),
                is_int($e->getCode()) ? $e->getCode() : 0,
                $e
            );
        }

        $messageId = $result->get('MessageId');

        $message->getOriginalMessage()->getHeaders()->addHeader('X-Message-ID', $messageId);
        $message->getOriginalMessage()->getHeaders()->addHeader('X-SES-Message-ID', $messageId);
    }

    /**
     * Extract the SES list managenent options, if applicable.
     *
     * @param  \Symfony\Component\Mailer\SentMessage  $message
     * @return array|null
     */
    protected function listManagementOptions(SentMessage $message)
    {
        if ($header = $message->getOriginalMessage()->getHeaders()->get('X-SES-LIST-MANAGEMENT-OPTIONS')) {
            if (preg_match("/^(contactListName=)*(?<ContactListName>[^;]+)(;\s?topicName=(?<TopicName>.+))?$/ix", $header->getBodyAsString(), $listManagementOptions)) {
                return array_filter($listManagementOptions, fn ($e) => in_array($e, ['ContactListName', 'TopicName']), ARRAY_FILTER_USE_KEY);
            }
        }
    }

    /**
     * Get the Amazon SES V2 client for the SesV2Transport instance.
     *
     * @return \Aws\SesV2\SesV2Client
     */
    public function ses()
    {
        return $this->ses;
    }

    /**
     * Get the transmission options being used by the transport.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the transmission options being used by the transport.
     *
     * @param  array  $options
     * @return array
     */
    public function setOptions(array $options)
    {
        return $this->options = $options;
    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'ses-v2';
    }
}
