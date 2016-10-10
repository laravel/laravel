<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Descriptor;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Tests\Fixtures\DescriptorApplication1;
use Symfony\Component\Console\Tests\Fixtures\DescriptorApplication2;
use Symfony\Component\Console\Tests\Fixtures\DescriptorCommand1;
use Symfony\Component\Console\Tests\Fixtures\DescriptorCommand2;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class ObjectsProvider
{
    public static function getInputArguments()
    {
        return array(
            'input_argument_1' => new InputArgument('argument_name', InputArgument::REQUIRED),
            'input_argument_2' => new InputArgument('argument_name', InputArgument::IS_ARRAY, 'argument description'),
            'input_argument_3' => new InputArgument('argument_name', InputArgument::OPTIONAL, 'argument description', 'default_value'),
        );
    }

    public static function getInputOptions()
    {
        return array(
            'input_option_1' => new InputOption('option_name', 'o', InputOption::VALUE_NONE),
            'input_option_2' => new InputOption('option_name', 'o', InputOption::VALUE_OPTIONAL, 'option description', 'default_value'),
            'input_option_3' => new InputOption('option_name', 'o', InputOption::VALUE_REQUIRED, 'option description'),
            'input_option_4' => new InputOption('option_name', 'o', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'option description', array()),
        );
    }

    public static function getInputDefinitions()
    {
        return array(
            'input_definition_1' => new InputDefinition(),
            'input_definition_2' => new InputDefinition(array(new InputArgument('argument_name', InputArgument::REQUIRED))),
            'input_definition_3' => new InputDefinition(array(new InputOption('option_name', 'o', InputOption::VALUE_NONE))),
            'input_definition_4' => new InputDefinition(array(
                new InputArgument('argument_name', InputArgument::REQUIRED),
                new InputOption('option_name', 'o', InputOption::VALUE_NONE),
            )),
        );
    }

    public static function getCommands()
    {
        return array(
            'command_1' => new DescriptorCommand1(),
            'command_2' => new DescriptorCommand2(),
        );
    }

    public static function getApplications()
    {
        return array(
            'application_1' => new DescriptorApplication1(),
            'application_2' => new DescriptorApplication2(),
        );
    }
}
