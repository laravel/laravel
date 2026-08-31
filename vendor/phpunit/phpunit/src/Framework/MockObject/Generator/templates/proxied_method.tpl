
    {modifier} function {reference}{method_name}({arguments_decl}){return_declaration}
    {
        $__phpunit_definedVariables        = get_defined_vars();
        $__phpunit_namedVariadicParameters = [];

        foreach ($__phpunit_definedVariables as $__phpunit_definedVariableName => $__phpunit_definedVariableValue) {
            if ((new ReflectionParameter([__CLASS__, __FUNCTION__], $__phpunit_definedVariableName))->isVariadic()) {
                foreach ($__phpunit_definedVariableValue as $__phpunit_key => $__phpunit_namedValue) {
                    if (is_string($__phpunit_key)) {
                        $__phpunit_namedVariadicParameters[$__phpunit_key] = $__phpunit_namedValue;
                    }
                }
            }
        }

        $__phpunit_arguments = [{arguments_call}];
        $__phpunit_count     = func_num_args();

        if ($__phpunit_count > {arguments_count}) {
            $__phpunit_arguments_tmp = func_get_args();

            for ($__phpunit_i = {arguments_count}; $__phpunit_i < $__phpunit_count; $__phpunit_i++) {
                $__phpunit_arguments[] = $__phpunit_arguments_tmp[$__phpunit_i];
            }
        }

        $__phpunit_arguments = array_merge($__phpunit_arguments, $__phpunit_namedVariadicParameters);

        $this->__phpunit_getInvocationHandler()->invoke(
            new \PHPUnit\Framework\MockObject\Invocation(
                '{class_name}', '{method_name}', $__phpunit_arguments, '{return_type}', $this, {clone_arguments}, true
            )
        );

        $__phpunit_result = call_user_func_array([$this->__phpunit_state()->proxyTarget(), "{method_name}"], $__phpunit_arguments);{return_result}
    }
