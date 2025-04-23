<script>
    /**
     * A front-end representation of a Backpack field, with its main components.
     *
     * Makes it dead-simple for the developer to perform the most common
     * javascript manipulations, and makes it easy to do custom stuff
     * too, by exposing the main components (name, wrapper, input).
     */
    class CrudField {
        constructor(name) {
            this.name = name;
            // get the current input
            this.$input = this.activeInput;
            // get the field wrapper
            this.wrapper = this.inputWrapper;

            // in case `bp-field-main-input` is specified on a field input, use that one as input
            this.$input = this.mainInput;

            // Validate that the wrapper has been found
            if (this.wrapper.length === 0) {
                console.error(`CrudField error! Could not select WRAPPER for "${this.name}"`);
            }

            // Validate that the field has been found
            if(this.$input.length === 0) {
                console.error(`CrudField error! Could not select INPUT for "${this.name}"`);
            }

            this.input = this.$input[0];
            this.type = this.wrapper.attr('bp-field-type');

            return this;

        }

        get activeInput() {
            // get the input/textarea/select that has that field name
            this.$input = $(`input[name="${this.name}"], textarea[name="${this.name}"], select[name="${this.name}"], select[name="${this.name}[]"]`);
            let possibleInput = this.$input.length === 1 ? this.$input : this.$input.filter(function() { return $(this).closest('[id=inline-create-dialog]').length });
            return possibleInput.length === 1 ? possibleInput : this.$input.first();
        }

        get mainInput() {
            let input = this.wrapper.find('[bp-field-main-input]').first();

            // if a bp-field-main-input has been specified by developer, that's it, use that one
            if (input.length !== 0) {
                return input;
            }

            // otherwise, try to find the input using other selectors
            if (this.$input.length === 0) {
                // try searching for the field with the corresponding bp-field-name
                input = this.wrapper.find(`input[bp-field-name="${this.name}"], textarea[bp-field-name="${this.name}"], select[bp-field-name="${this.name}"], select[bp-field-name="${this.name}[]"]`).first();

                // if not input found yet, just get the first input in that wrapper
                if (input.length === 0) {
                    input = this.wrapper.find('input, textarea, select').first();
                }

                return input;
            }

            return this.$input;

        }

        get value() {
            return this.$input.val();
        }

        get inputWrapper() {
            let wrapper = this.$input.closest('[bp-field-wrapper]');
            if (wrapper.length === 0) {
                wrapper = $(`[bp-field-name="${this.name}"][bp-field-wrapper]`).first();
            }
            return wrapper;
        }

        onChange(closure) {
            const bindedClosure = closure.bind(this);
            const fieldChanged = (event, values) => bindedClosure(this, event, values);

            if(this.isSubfield) {
                window.crud.subfieldsCallbacks[this.parent.name] ??= [];
                window.crud.subfieldsCallbacks[this.parent.name].push({ closure, field: this });

                this.parent.wrapper.trigger('CrudField:subfieldCallbacksUpdated');
                return this;
            }

            if(['INPUT', 'TEXTAREA'].includes(this.input?.nodeName)) {
                this.input?.addEventListener('input', fieldChanged, false);
            }
            this.$input.change(fieldChanged);

            return this;
        }

        change() {
            if(this.isSubfield) {
                window.crud.subfieldsCallbacks[this.parent.name]?.forEach(function(callback) {
                    if(callback.field.name === this.name) {
                        callback.triggerChange = true;
                    }
                }, this);
            } else {
                let event = new Event('change');
                this.input?.dispatchEvent(event);
            }

            return this;
        }

        show(value = true) {
            this.wrapper.toggleClass('d-none', !value);
            let event = new Event(`CrudField:${value ? 'show' : 'hide'}`);
            this.input?.dispatchEvent(event);
            return this;
        }

        hide(value = true) {
            return this.show(!value);
        }

        enable(value = true) {
            this.$input.attr('disabled', !value && 'disabled');
            let event = new Event(`CrudField:${value ? 'enable' : 'disable'}`);
            this.input?.dispatchEvent(event);
            return this;
        }

        disable(value = true) {
            return this.enable(!value);
        }

        require(value = true) {
            this.wrapper.toggleClass('required', value);
            let event = new Event(`CrudField:${value ? 'require' : 'unrequire'}`);
            this.input?.dispatchEvent(event);
            return this;
        }

        unrequire(value = true) {
            return this.require(!value);
        }

        check(value = true) {
            this.wrapper.find('input[type=checkbox]').prop('checked', value).trigger('change');
            return this;
        }

        uncheck(value = true) {
            return this.check(!value);
        }

        subfield(name, rowNumber = false) {
            let subfield = new CrudField(this.name);
            subfield.name = name;
            subfield.parent = this;                
            

            if(!rowNumber) {
                subfield.isSubfield = true;
                subfield.subfieldHolder = this.name; // deprecated
            } else {
                subfield.rowNumber = rowNumber;
                subfield.wrapper = $(`[data-repeatable-identifier="${this.name}"][data-row-number="${rowNumber}"]`).find(`[bp-field-wrapper][bp-field-name$="${name}"]`);
                subfield.$input = subfield.wrapper.find(`[data-repeatable-input-name$="${name}"][bp-field-main-input]`);
                // if no bp-field-main-input has been declared in the field itself,
                // assume it's the first input in that wrapper, whatever it is
                if (subfield.$input.length == 0) {
                    subfield.$input = subfield.wrapper.find(`input[data-repeatable-input-name$="${name}"], textarea[data-repeatable-input-name$="${name}"], select[data-repeatable-input-name$="${name}"]`).first();
                }

                subfield.input = subfield.$input[0];
            }
            return subfield;
        }
    }

    /**
     * Window functions that help the developer easily select one or more fields.
     */
    window.crud = {
        ...window.crud,

        action: "{{ $action ?? "" }}",

        // Subfields callbacks holder
        subfieldsCallbacks: [],

        // Create a field from a given name
        field: name => new CrudField(name),

        // Create all fields from a given name list
        fields: names => names.map(window.crud.field),
    };
</script>
