# This file is part of the Symfony package.
#
# (c) Fabien Potencier <fabien@symfony.com>
#
# For the full copyright and license information, please view
# https://symfony.com/doc/current/contributing/code/license.html

_sf_{{ COMMAND_NAME }}() {

    # Use the default completion for shell redirect operators.
    for w in '>' '>>' '&>' '<'; do
        if [[ $w = "${COMP_WORDS[COMP_CWORD-1]}" ]]; then
            compopt -o filenames
            COMPREPLY=($(compgen -f -- "${COMP_WORDS[COMP_CWORD]}"))
            return 0
        fi
    done

    # Use newline as only separator to allow space in completion values
    local IFS=$'\n'
    local sf_cmd="${COMP_WORDS[0]}"

    # for an alias, get the real script behind it
    sf_cmd_type=$(type -t $sf_cmd)
    if [[ $sf_cmd_type == "alias" ]]; then
        sf_cmd=$(alias $sf_cmd | sed -E "s/alias $sf_cmd='(.*)'/\1/")
    elif [[ $sf_cmd_type == "file" ]]; then
        sf_cmd=$(type -p $sf_cmd)
    fi

    if [[ $sf_cmd_type != "function" && ! -x $sf_cmd ]]; then
        return 1
    fi

    local cur prev words cword
    _get_comp_words_by_ref -n := cur prev words cword

    local completecmd=("$sf_cmd" "_complete" "--no-interaction" "-sbash" "-c$cword" "-a{{ VERSION }}")
    for w in ${words[@]}; do
        w="${w//\\\\/\\}"
        # remove quotes from typed values
        quote="${w:0:1}"
        if [ "$quote" == \' ]; then
            w="${w%\'}"
            w="${w#\'}"
        elif [ "$quote" == \" ]; then
            w="${w%\"}"
            w="${w#\"}"
        fi
        # empty values are ignored
        if [ ! -z "$w" ]; then
            completecmd+=("-i$w")
        fi
    done

    local sfcomplete
    if sfcomplete=$(SHELL_VERBOSITY=0 ${completecmd[@]} 2>&1); then
        local quote suggestions
        quote=${cur:0:1}

        # Use single quotes by default if suggestions contains backslash (FQCN)
        if [ "$quote" == '' ] && [[ "$sfcomplete" =~ \\ ]]; then
            quote=\'
        fi

        if [ "$quote" == \' ]; then
            # single quotes: no additional escaping (does not accept ' in values)
            suggestions=$(for s in $sfcomplete; do printf $'%q%q%q\n' "$quote" "$s" "$quote"; done)
        elif [ "$quote" == \" ]; then
            # double quotes: double escaping for \ $ ` "
            suggestions=$(for s in $sfcomplete; do
                s=${s//\\/\\\\}
                s=${s//\$/\\\$}
                s=${s//\`/\\\`}
                s=${s//\"/\\\"}
                printf $'%q%q%q\n' "$quote" "$s" "$quote";
            done)
        else
            # no quotes: double escaping
            suggestions=$(for s in $sfcomplete; do printf $'%q\n' $(printf '%q' "$s"); done)
        fi
        COMPREPLY=($(IFS=$'\n' compgen -W "$suggestions" -- $(printf -- "%q" "$cur")))
        __ltrim_colon_completions "$cur"
    else
        if [[ "$sfcomplete" != *"Command \"_complete\" is not defined."* ]]; then
            >&2 echo
            >&2 echo $sfcomplete
        fi

        return 1
    fi
}

complete -F _sf_{{ COMMAND_NAME }} {{ COMMAND_NAME }}
