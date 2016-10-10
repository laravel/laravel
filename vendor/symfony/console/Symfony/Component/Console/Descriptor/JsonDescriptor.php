<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Descriptor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * JSON descriptor.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class JsonDescriptor extends Descriptor
{
    /**
     * {@inheritdoc}
     */
    protected function describeInputArgument(InputArgument $argument, array $options = array())
    {
        $this->writeData($this->getInputArgumentData($argument), $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function describeInputOption(InputOption $option, array $options = array())
    {
        $this->writeData($this->getInputOptionData($option), $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function describeInputDefinition(InputDefinition $definition, array $options = array())
    {
        $this->writeData($this->getInputDefinitionData($definition), $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function describeCommand(Command $command, array $options = array())
    {
        $this->writeData($this->getCommandData($command), $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function describeApplication(Application $application, array $options = array())
    {
        $describedNamespace = isset($options['namespace']) ? $options['namespace'] : null;
        $description = new ApplicationDescription($application, $describedNamespace);
        $commands = array();

        foreach ($description->getCommands() as $command) {
            $commands[] = $this->getCommandData($command);
        }

        $data = $describedNamespace
            ? array('commands' => $commands, 'namespace' => $describedNamespace)
            : array('commands' => $commands, 'namespaces' => array_values($description->getNamespaces()));

        $this->writeData($data, $options);
    }

    /**
     * Writes data as json.
     *
     * @param array $data
     * @param array $options
     *
     * @return array|string
     */
    private function writeData(array $data, array $options)
    {
        $this->write(json_encode($data, isset($options['json_encoding']) ? $options['json_encoding'] : 0));
    }

    /**
     * @param InputArgument $argument
     *
     * @return array
     */
    private function getInputArgumentData(InputArgument $argument)
    {
        return array(
            'name'        => $argument->getName(),
            'is_required' => $argument->isRequired(),
            'is_array'    => $argument->isArray(),
            'description' => $argument->getDescription(),
            'default'     => $argument->getDefault(),
        );
    }

    /**
     * @param InputOption $option
     *
     * @return array
     */
    private function getInputOptionData(InputOption $option)
    {
        return array(
            'name'              => '--'.$option->getName(),
            'shortcut'          => $option->getShortcut() ? '-'.implode('|-', explode('|', $option->getShortcut())) : '',
            'accept_value'      => $option->acceptValue(),
            'is_value_required' => $option->isValueRequired(),
            'is_multiple'       => $option->isArray(),
            'description'       => $option->getDescription(),
            'default'           => $option->getDefault(),
        );
    }

    /**
     * @param InputDefinition $definition
     *
     * @return array
     */
    private function getInputDefinitionData(InputDefinition $definition)
    {
        $inputArguments = array();
        foreach ($definition->getArguments() as $name => $argument) {
            $inputArguments[$name] = $this->getInputArgumentData($argument);
        }

        $inputOptions = array();
        foreach ($definition->getOptions() as $name => $option) {
            $inputOptions[$name] = $this->getInputOptionData($option);
        }

        return array('arguments' => $inputArguments, 'options' => $inputOptions);
    }

    /**
     * @param Command $command
     *
     * @return array
     */
    private function getCommandData(Command $command)
    {
        $command->getSynopsis();
        $command->mergeApplicationDefinition(false);

        return array(
            'name'        => $command->getName(),
            'usage'       => $command->getSynopsis(),
            'description' => $command->getDescription(),
            'help'        => $command->getProcessedHelp(),
            'aliases'     => $command->getAliases(),
            'definition'  => $this->getInputDefinitionData($command->getNativeDefinition()),
        );
    }
}
