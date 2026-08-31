# This file is part of the Symfony package.
#
# (c) Fabien Potencier <fabien@symfony.com>
#
# For the full copyright and license information, please view
# https://symfony.com/doc/current/contributing/code/license.html

function _sf_{{ COMMAND_NAME }}
    set sf_cmd (commandline -o)
    set c (count (commandline -oc))

    set completecmd "$sf_cmd[1]" "_complete" "--no-interaction" "-sfish" "-a{{ VERSION }}"

    for i in $sf_cmd
        if [ $i != "" ]
            set completecmd $completecmd "-i$i"
        end
    end

    set completecmd $completecmd "-c$c"

    set sfcomplete (env SHELL_VERBOSITY=0 $completecmd)

    for i in $sfcomplete
        echo $i
    end
end

complete -c '{{ COMMAND_NAME }}' -a '(_sf_{{ COMMAND_NAME }})' -f
