/**
 * SheepIt! Jquery Plugin
 * http://www.mdelrosso.com/sheepit/
 *
 * @version 1.1.1
 *
 * Created By Mariano Del Rosso (http://www.mdelrosso.com)
 *
 * Thanks to:
 *  Hubert Galuszka: Continuous index option and support for tabular forms
 *  Gabriel Alonso: Bugfixes
 *
 * @license
 * 
 * SheepIt is free software: you can redistribute it and/or modify
 * it under the terms of the MIT license
 * 
 * SheepIt is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * MIT license for more details.
 * 
 * You should have received a copy of the MIT license
 * along with SheepIt.  If not, see <http://en.wikipedia.org/wiki/MIT_License>.
 */

(function($){

    jQuery.fn.sheepIt = function (options){


        /**
         * Clone the form template
         */
        function cloneTemplate()
        {
            var clone;

            // Before clone callBack function
            if (typeof options.beforeClone === "function") {
                options.beforeClone(source, template);
            }
            clone = template.cloneWithAttribut(true);

            // After clone callBack function
            if (typeof options.afterClone === "function") {
                options.afterClone(source, clone);
            }

            // Get source
            clone.getSource = function() {
                return source;
            };

            return clone;
            
        }

        /**
         * Handle click on addForm button
         */
        function clickOnAdd(event)
        {
            event.preventDefault();
            addForm();
        }

        /**
         * Handle click on addNForm button
         */
        function clickOnAddN(event)
        {
            event.preventDefault();
            if (addNInput.value !== '') {
                addNForms(addNInput.attr('value'));
            }
        }

        /**
         * Handle click on Remove current button
         */
        function clickOnRemoveCurrent(event)
        {
            event.preventDefault();

            // Before remove current callBack function
            if (typeof options.beforeRemoveCurrent === "function") {
                options.beforeRemoveCurrent(source);
            }
            
            if (options.removeCurrentConfirmation) {
                if ( confirm(options.removeCurrentConfirmationMsg) ) {
                    removeCurrentForm($(this).data('removableClone'));
                }
            } else {
                removeCurrentForm($(this).data('removableClone'));
            }
            
            // After remove current callBack function
            if (typeof options.afterRemoveCurrent === "function") {
                options.afterRemoveCurrent(source);
            }
        }

        /**
         * Handle click on Remove last control
         */
        function clickOnRemoveLast(event)
        {
            event.preventDefault();

            if (options.removeLastConfirmation) {
                if ( confirm(options.removeLastConfirmationMsg) ) {
                    removeLastForm();
                }
            } else {
                removeLastForm();
            }


        }

        /**
         * Handle click on Remove all control
         */
        function clickOnRemoveAll(event)
        {
            event.preventDefault();

            if (options.removeAllConfirmation) {
                if ( confirm(options.removeAllConfirmationMsg) ) {
                    removeAllForms();
                }
            } else {
                removeAllForms();
            }


        }
        
        function getOrSetTemplate(element, attrname){
          var template=element.attr(attrname+"template");
          if(template) {
            return unescape(template);
          }
          var att=element.attr(attrname);
          // Hide index occurrences inside the template (todo: better escaping method)      
          element.attr(attrname+"template", escape(att));
          return att;
        }

        /**
         * Get a form and normalize fields id and names to match the current position
         */
        function normalizeFieldsForForm(form, index)
        {
            form.find(formFields).each(function(){
                var that = $(this)
                    ,idTemplateAttr = getOrSetTemplate(that,"id")
                    ,nameTemplateAttr = getOrSetTemplate(that, "name")
                    ,idAttr = that.attr("id")
                    ,nameAttr = that.attr("name")
                    
                /* Normalize field name attributes */
                newNameAttr = nameTemplateAttr.replace(options.indexFormat, index);
                that.attr("name", newNameAttr);

                /* Normalize field id attributes */
                newIdAttr = idTemplateAttr.replace(options.indexFormat, index);

                form.find("label[for='"+idAttr+"']").each(function(){
                        $(this).attr("for", newIdAttr);
                    });
                that.attr("id", newIdAttr);
            });
        }

        function normalizeLabelsForForm(form, index)
        {
            setLabelForForm(form, index+1);
        }

        function setLabelForForm(form, label)
        {
            form.find(options.labelSelector).html(label);
            return true;
        }

        function getLabelForForm(form)
        {
            return form.find(options.labelSelector).html();
        }

        /**
         * Show/Hide controls according to current state of the forms
         */
        function normalizeControls()
        {
            // Remove buttons
            if (hasForms()) {

                if (getFormsCount() == 1) {
                    removeAll.hideIf();
                    removeLast.showIf();
                } else {
                    removeAll.showIf();
                    removeLast.showIf();
                }

                // Remove current buttons
                var removeCurrents = '';
                if (options.allowRemoveCurrent) {
                    removeCurrents = source.find(options.removeCurrentSelector);
                    if (canRemoveForm()) {
                        // Show remove current buttons of all forms
                        removeCurrents.show();
                    } else {
                        removeCurrents.hide();
                    }
                } else {
                    // Hide all
                    removeCurrents = source.find(options.removeCurrentSelector);
                    removeCurrents.hide();
                }



            } else {
                removeLast.hideIf();
                removeAll.hideIf();
            }

            // Add button
            if (!canAddForm()) {
                add.hideIf();
                addN.hideIf();
            } else {
                add.showIf();
                addN.showIf();
            }

            // Remove buttons only enabled when can remove forms
            if (!canRemoveForm()) {
                removeLast.hideIf();
                removeAll.hideIf();
            }

            if (
                   add.css('display') != 'none'
                || addN.css('display') != 'none'
                || removeAll.css('display') != 'none'
                || removeLast.css('display') != 'none'
            ) {
                controls.show();
            } else {
                controls.hide();
            }
        }

        /**
         * Show/hide noFormsMsg
         */
        function normalizeForms()
        {
          if(hasForms()){
              
            noFormsTemplate.hide();
            
            if(options.continuousIndex) {
                
              var index=0
                , form=getFirstForm();
              
              do{
                normalizeForm(form, index);
                index++;
                form = getNextForm(form);
              }while (form!=false)
            }
            
            
          }else{
            noFormsTemplate.show();
          }
        }

        function normalizeForm(form, index)
        {
            if (typeof index == 'undefined') {
                index=getIndex();
            }
            
            var idTemplate=getOrSetTemplate(form, "id");

            // Normalize form id
            if (form.attr("id")) {
                form.attr("id", idTemplate + index);
            }
            
            
            // Normalize indexes for fields name and id attributes
            normalizeFieldsForForm(form, index);

            // Normalize labels
            normalizeLabelsForForm(form, index);

            // Normalize other possibles indexes inside html
            if (form.html().indexOf(options.indexFormat) != -1) {
                // Create a javascript regular expression object
                var re = new RegExp(options.indexFormat,"ig");
                // Replace all index occurrences inside the html
                form.html(form.html().replace(re, index));
            }
            
            // Remove current form control
            var removeCurrent = form.find(options.removeCurrentSelector);
            (options.allowRemoveCurrent) ? removeCurrent.show() : removeCurrent.hide();

            return form;
        }

        /**
         * Normalize all (Controls, Forms)
         */
        function normalizeAll()
        {
            normalizeForms();
            normalizeControls();
        }

        /**
         * Add a new form to the collection
         * 
         * @parameter normalize: avoid normalize all forms if not necessary
         */
        function addForm(normalizeAllafterAdd, form)
        {
            if (typeof normalizeAllafterAdd == 'undefined') {
                normalizeAllafterAdd = true;
            }
            
            if (typeof form == 'undefined') {
                form = false;
            }

            // Before add callBack function
            if (typeof options.beforeAdd === "function") {
                options.beforeAdd(source);
            }
                
            var newForm = false;
            
            // Pre-generated form
            if (form) {
                if ( typeof(form) == 'string' ) {
                    newForm = $('#' + form);
                }
                else if ( typeof(form) == 'object' ) {
                   newForm = form;
                } else {
                    return false;
                }
                
                newForm.remove();
               
            }
            // Cloned Form
            else {
                // Get template clone
                newForm = cloneTemplate();
            }

            if (canAddForm() && newForm) {
                
                newForm = normalizeForm(newForm);
                

                // Remove current control
                var removeCurrentBtn = newForm.find(options.removeCurrentSelector).first();

                removeCurrentBtn.click(clickOnRemoveCurrent);
                removeCurrentBtn.data('removableClone', newForm);
                
                
                // Index
                newForm.data('formIndex', getIndex());
                
                // Linked references (separators and forms)
                newForm.data('previousSeparator',false);
                newForm.data('nextSeparator',false);
                newForm.data('previousForm',false);
                newForm.data('nextForm',false);

                // Link references?
                if (hasForms()) {

                    var lastForm = getLastForm();

                    // Form references
                    lastForm.data('nextForm',newForm);
                    newForm.data('previousForm',lastForm);

                    // Separator references
                    if (options.separator) {
                        var separator = getSeparator();
                        separator.insertAfter(lastForm);
                        lastForm.data('nextSeparator',separator);
                        newForm.data('previousSeparator',separator);
                    }

                }

                (options.insertNewForms == 'after') ? newForm.insertBefore(noFormsTemplate) : newForm.insertAfter(noFormsTemplate);

                // Nested forms
                if (options.nestedForms.length > 0) {

                    var x = 0;
                    var nestedForms = [];
                    
                    for(x in options.nestedForms) {

                        if (typeof(options.nestedForms[x].id) != 'undefined' && typeof(options.nestedForms[x].options) != 'undefined') {
                            options.nestedForms[x].isNestedForm = true;
                            options.nestedForms[x].parentForm = source;
                            var id = options.nestedForms[x].id.replace(options.indexFormat,newForm.data('formIndex'));
                            var nestedForm = $('#' + id).sheepIt(options.nestedForms[x].options);
                            
                            nestedForms.push(nestedForm);
                        }
                    }
                    newForm.data('nestedForms',nestedForms);
                }

                extendForm(newForm);
                
                forms.push(newForm);

                /**
                 * If index has to be continuous,
                 * all items are reindexed/renumbered using 
                 * normalizeAll() after add a new form clone
                 */
                if (normalizeAllafterAdd || options.continuousIndex) {
                    normalizeAll();
                }

                // After add callBack function
                if (typeof options.afterAdd === "function") {
                    options.afterAdd(source, newForm);
                }

                return true;
                
            } else {
                return false;
            }

        }

        function addNForms(n, normalize)
        {
            if (typeof n != 'undefined') {
                n = parseFloat(n);
                var x = 1;

                for(x=1; x<=n; x++) {
                    addForm(normalize);
                }
            }
        }

        function removeLastForm(normalize)
        {
            if (typeof normalize == 'undefined') {
                normalize = true;
            }

            if (canRemoveForm()) {
                removeForm();

                if (normalize) {
                    normalizeAll();
                }
                return true;

            } else {
                return false;
            }

        }

        function removeAllForms(normalize)
        {
            if (typeof normalize == 'undefined') {
                normalize = true;
            }

            if (canRemoveAllForms()) {
                var x = [];
                for (x in forms) {
                    if (forms[x]) {
                        removeForm(forms[x]);
                    }
                }

                if (normalize) {
                    normalizeAll();
                }
                return true;
            } else {
                return false;
            }

        }

        function removeCurrentForm(formToRemove, normalize)
        {
            if (typeof normalize == 'undefined') {
                normalize = true;
            }

            if (canRemoveForm()) {
                removeForm(formToRemove);

                if (normalize) {
                    normalizeAll();
                }
                return true;
            } else {
                return false;
            }
        }

        /**
         * Remove form from the index and DOM
         */
        function removeForm(formToRemove)
        {
            // If no form provided then remove the last one
            if (typeof formToRemove == 'undefined') {
                formToRemove = getLastForm();
            }

            index = formToRemove.data('formIndex');

            /**
             * Remove separator?
             */
            // Two
            if (formToRemove.data('previousSeparator') && formToRemove.data('nextSeparator')) {
                formToRemove.data('previousSeparator').remove();
                formToRemove.data('previousForm').data('nextSeparator',formToRemove.data('nextSeparator'));
            }
            // before
            else if(formToRemove.data('previousSeparator') && !formToRemove.data('nextSeparator')) {
                formToRemove.data('previousSeparator').remove();
                formToRemove.data('previousForm').data('nextSeparator',false);
            }
            // after
            else if(!formToRemove.data('previousSeparator') && formToRemove.data('nextSeparator')) {
                formToRemove.data('nextSeparator').remove();
                formToRemove.data('nextForm').data('previousSeparator',false);
            }

            // Update forms references
            if (formToRemove.data('previousForm')) {
                formToRemove.data('previousForm').data('nextForm',formToRemove.data('nextForm'));
            }

            if (formToRemove.data('nextForm')) {
                formToRemove.data('nextForm').data('previousForm',formToRemove.data('previousForm'));
            }

            // From index
            forms[index] = false;

            // From DOM
            formToRemove.remove();

            return true;

        }



        /*---------------- ITERATOR METHODS ----------------*/

        /**
         * Gets the current internal pointer
         */
        function current()
        {
            return ip; // false or integer
        }

        /**
         * Increment the internal pointer
         */
        function next()
        {
            if (ip !== false) {
                
                if (forms.length > 1) {
                    var i = 0;
                    var init = parseFloat(ip+1);
                    
                    for (i=init; i<forms.length; i++) {
                        if (forms[i]) {
                            ip = i;
                            return true;
                        }
                    }
                    return false;
                } else {
                    return false;
                }

            } else {
                return false;
            }
        }

        /**
         * Decrement the internal pointer
         */
        function previous()
        {
            if (ip !== false) {

                if (forms.length > 1) {

                    var i = 0;
                    var init = parseFloat(ip-1);
                    for (i=init; i>=0; i--) {

                        if (forms[i]) {
                            ip = i;
                            return true;
                        }
                    }
                    return false;
                } else {
                    return false;
                }

            } else {
                return false;
            }
        }

        /**
         * Brings the internal pointer to the first element
         */
        function first()
        {
            ip = false;
            if (forms.length > 0) {
                var x = 0;
                for (x in forms) {

                    if (forms[x]) {
                        ip = x;
                        return true;
                    }
                }
                return false;
            } else {
                return false;
            }
        }

        /**
         * Brings the internal pointer to the last element
         */
        function last()
        {
            ip = false;
            if (forms.length > 0) {

                if (forms[forms.length-1]) {
                    ip = forms.length-1;
                    return true;
                } else {
                    var i = 0;
                    for (i=(forms.length-1); i>=0 ; i--) {
                        
                        if (forms[i]) {
                            ip = i;
                            return true;
                        }
                    }
                    return false;
                }
            } else {
                return false;
            }
        }

        /**
         * Count the current elements
         */
        function count()
        {
            if (forms.length > 0) {
                var count = 0;
                var x = [];
                for (x in forms) {
                    if (forms[x] ) {
                        count++;
                    }
                }
                return count;
            } else {
                return 0;
            }
        }

        /**
         * Sets the pointer to a new position
         */
        function setPointerTo(position)
        {
            if (typeof position != 'undefined') {
                ip = getIndexForPosition(position);
                if (ip !== false) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        /**
         * Get the "real" index for a given position
         */
        function getIndexForPosition(position)
        {
            var x = 0;
            var count = 0;
            var index = false;

            for (x in forms) {
                if (forms[x]) {
                    count++;
                    // get index for position
                    if (position == count) {
                        index = x;
                    }
                }
            }

            return index;
        }

        function getPositionForIndex(index)
        {
            var x = 0;
            var position = 0;
            
            for (x=0; x<=index; x++) {
                if (forms[x]) {
                    position++;
                }
            }
            return position;
        }
        
        /**
         * Get the current index (Forms array length)
         */
        function getIndex()
        {
            return forms.length;
        }

        /*---------------- /ITERATOR METHODS ----------------*/

        function getFormsCount()
        {
            return count();
        }

        function getFirstForm()
        {
            if (first() !== false) {
                return getCurrentForm();
            } else {
                return false;
            }
        }

        function getLastForm()
        {
            if (last() !== false) {
                return getCurrentForm();
            } else {
                return false;
            }
        }

        function getNextForm(form)
        {
            if (form) {
                return form.data('nextForm');
            } else if(current() !== false) {
                if (next() !== false) {
                    return getCurrentForm();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function getPreviousForm(form)
        {
            if (form) {
                return form.data('previousForm');
            } else if(current() !== false) {
                if (previous() !== false ) {
                    return getCurrentForm();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        /**
         * Get the current form based on the interal pointer
         */
        function getCurrentForm()
        {
            if ( current() !== false) {
                return forms[current()];
            } else {
                return false;
            }
        }
        
        /**
         * Get a form by its position
         */
        function getForm(position)
        {
            if (hasForms()) {
                if (typeof(position) != 'undefined') {
                    setPointerTo(position);
                    return getCurrentForm();
                }
                // Last form
                else {
                    return getLastForm();
                }
            } else {
                return false;
            }
        }

        /**
         * Get active forms
         */
        function getForms()
        {
            if (hasForms()) {
               first();
               
               var x = 0;
               var activeForms = [];
               for (x=0; x<getFormsCount(); x++) {
                   activeForms.push(getCurrentForm());
                   next();
               }
               return activeForms;
            } else {
                return false;
            }
        }

        function hasForms()
        {
            return (getFormsCount() > 0) ? true : false;
        }

        function canAddForm()
        {
            if (options.maxFormsCount == 0) {
                return true;
            } else {
                return (getFormsCount() < options.maxFormsCount) ? true : false;
            }
        }

        /**
         * Checks if can remove any form
         */
        function canRemoveForm()
        {
            return (getFormsCount() > options.minFormsCount) ? true : false;
        }

        function canRemoveAllForms()
        {
           return (options.minFormsCount == 0) ? true : false;
        }

        function isInDom(object)
        {
            if ( $("#" + object.attr('id')).length > 0 ) {
                 return true;
            } else {
                return false;
            }
        }

        /**
         * Controls the whole process of data injection
         *
         */
        function fillData(index, values)
        {
            
            var form = '';

            // Position
            if (typeof(index) == 'number') {

                // Correction of index to position
                index++;

                // Need more forms?
                if ((index) > getFormsCount()) {
                   addForm();
                }

                form = getForm(index);
                
                fillForm(form, values);
            } 
            // Form Id
            else if(typeof(index) == 'string') {
                
                form = $('#'+index);
                fillForm(form, values);
            }
            
            if (typeof options.afterFill === "function") {
                options.afterFill(source, form, values);
            }
                
        }

        function fillForm(form, data)
        {
            var x = 0;

            // For each element, try to get the correct field or fields
            $.each(data, function(index, value) {
                
                var formId = source.attr('id');
                var formIndex = form.data('formIndex');



                // Replace form Id and form Index with current values
                if (index.indexOf('#form#') != -1 || index.indexOf('#index#') != -1) {
                    index = index.replace('#form#', formId);
                    index = index.replace('#index#', formIndex);
                } else {
                    index = formId + '_' + formIndex + '_' + index;
                }
                
              

                /**
                 * Search for field (by id, by name, etc)
                 */
                
                // Search by id
                var field = form.find(':input[id="' + index + '"]');

                // Search by name
                if (field.length == 0) {

                    // Search by name
                    field = form.find(':input[name="' + index + '"]');

                    if (field.length == 0) {
                        // Search by name array format
                        field = form.find(':input[name="' + index + '[]"]');
                    } 
                }
                
                

                // Field was found
                if (field.length > 0) {
					
                    // Multiple values?
                    var mv = false;
                    if (typeof(value) == 'object') {
                        mv = true;
                    }

                    // Multiple fields?
                    var mf = false;
                    if (field.length > 1) {
                        mf = true;
                    }

                    if (mf) {

                        if (mv) {
							
                            var fieldsToFill = [];
                            fieldsToFill['fields'] = [];
                            fieldsToFill['values'] = [];

                            x = 0;
                            for (x in value) {
                                 fieldsToFill['fields'].push(field.filter('[value="'+ value[x] +'"]'));
                                 fieldsToFill['values'].push(value[x]);
                            }
                            x = 0;
                            for (x in fieldsToFill['fields']) {
                                fillFormField(fieldsToFill['fields'][x] , fieldsToFill['values'][x]);
                            }
                        } else {
                            fillFormField( field.filter('[value="'+ value +'"]', value) );
                        }
                    } else {
                        if (mv) {
                            x = 0;
                            for (x in value) {
                                fillFormField(field, value[x]);
                            }
                        } else {
                           fillFormField(field, value);
                        }
                    }
                }
                // Field not found in this form try search inside nested forms
                else {
                    if ( typeof(form.data('nestedForms')) != 'undefined') {
                        if (form.data('nestedForms').length > 0) {
                            x = 0;
                            for (x in form.data('nestedForms')) {

                                if (index == form.data('nestedForms')[x].attr('id') && typeof(value) == 'object') {
                                    form.data('nestedForms')[x].inject(value);
                                }
                            }

                        }
                    }
                }
                
            });
            

        }

        function fillFormField(field, value)
        {
            var type = field.attr('type');

            // hidden, text, password
            if (type == 'text' || type == 'hidden' || type == 'password') {
                field.attr('value', value);
                return true;
            }
            // textarea
            else if(type == 'textarea') {
                field.text(value);
                return true;
            }
            // checkbox, radio button
            else if(type == 'checkbox' || type == 'radio') {
                field.attr("checked", "checked");
                return true;
            }
            // select-one, select-multiple
            else if (type == 'select-one' || type == 'select-multiple') {
                field.find("option").each(function() {
                    if($(this).text() == value || $(this).attr("value") == value) {
                            $(this).attr("selected", "selected");
                    }
                });
                return true;
            } else {
                return false;
            }
        }

        function hasSeparator()
        {
            if (options.separator != '') {
                return true;
            } else {
                return false;
            }
        }

        function getSeparator()
        {
            if (hasSeparator()) {
                return $(options.separator);
            } else {
                return false;
            }
        }

        function setOptions(newOptions) 
        {
            options = [];
            options = $.extend(defaults, newOptions);
            normalizeOptions(options);
        }

        function getOptions()
        {
            return options;
        }

        function initialize()
        {
            // Hide forms during initialization
            source.hide();

            /**
             * Controls
             */
            add = $(options.addSelector);
            addN = $(options.addNSelector);
            addNInput = $(options.addNInputSelector);
            addNButton = $(options.addNButtonSelector);
            removeLast = $(options.removeLastSelector);
            removeCurrent = $(options.removeCurrentSelector);
            removeAll = $(options.removeAllSelector);
            controls = $(options.controlsSelector);

            if (add.length == 0) {
                options.allowAdd = false;
            }
            if (addN.length == 0) {
                options.allowAddN = false;
            }
            if (removeLast.length == 0) {
                options.allowRemoveLast = false;
            }
            if (removeAll.length == 0) {
                options.allowRemoveAll = false;
            }

            // Extend basic controls with new methods used inside this plugin
            extendControl(add, options.allowAdd, clickOnAdd);
            extendControl(addN, options.allowAddN, clickOnAddN, addNButton);
            extendControl(removeLast, options.allowRemoveLast, clickOnRemoveLast);
            extendControl(removeAll, options.allowRemoveAll, clickOnRemoveAll);

            // Initialize controls
            add.init();
            addN.init();
            removeLast.init();
            removeAll.init();

            /**
             * Templates
             */
            templateForm = $(options.formTemplateSelector);
            noFormsTemplate = $(options.noFormsTemplateSelector);
            
            // Get the template for clonning
            template = templateForm.cloneWithAttribut(true);
            templateForm.remove();

            /**
             * Forms initialization
             */
            var x = 0;

            // Pregenerated forms
            if (options.pregeneratedForms.length > 0) {
                x = 0;
                for(x in options.pregeneratedForms) {
                    addForm(false,options.pregeneratedForms[x]);
                }
            }

            // Initial forms
            if ( options.iniFormsCount > getFormsCount()) {
                x = 0;
                var b = options.iniFormsCount-getFormsCount();
                for (x=1; x<=b; x++) {
                    addForm(false);
                }

            }

            /**
             * Data injection
             */
            if(options.data){
                source.inject(options.data);
            }

            normalizeAll();

            source.show();
        }

        /**
         * Extend passed control with new methods used by this plugin
         */
        function extendControl(control, allowControlOption , onClickFunction, onClickSubControl)
        {
            /**
             * onClickSubControl es utilizado cuando el control principal no es el que recibe el click
             */
            if (typeof(onClickSubControl) == 'undefined') {
                onClickSubControl = false;
            }

            $.extend( control, {
                hideIf : function(duration, callback) {
                    if (allowControlOption) {
                        control.hide(duration, callback);
                    }
                },
                showIf: function(duration, callback) {
                    if (allowControlOption) {
                        control.show(duration, callback);
                    }
                },
                init: function() {
                    if (allowControlOption) {
                        // Click event
                        if (onClickSubControl) {
                            onClickSubControl.click(onClickFunction);
                        } else {
                            control.click(onClickFunction);
                        }
                        control.show();
                    } else {
                        control.hide();
                    }
                }
            });
        }

        /**
         * Extends source object with many useful methods,
         * used to control sheepIt forms with javascript
         */
        function extendSource(source)
        {
            // API
            $.extend( source, {

                    /* ----- Controls ----- */
                    getAddControl: function() {
                        return add;
                    },
                    getAddNControl: function() {
                        return addN;
                    },
                    getRemoveLastControl: function() {
                        return removeLast;
                    },
                    getRemoveAllControl: function() {
                        return removeAll;
                    },

                    /* ----- Options ----- */
                    getOptions: function() {
                        return getOptions();
                    },
                    getOption: function(option) {
                        return options[option];
                    },
                    setOption: function(option, value) {
                        if (typeof(option) != 'undefined' && typeof(value) != 'undefined') {
                            options[option] = value;
                            return options[option];
                        } else {
                            return false;
                        }
                    },
                   
                    /* ----- Forms ----- */
                    // Get all Forms
                    getForms: function() {
                        return getForms();
                    },
                    // Alias of getForms
                    getAllForms: function() {
                        return getForms();
                    },
                    getForm: function(val) {
                        if (typeof(val) != 'undefined') {
                            val++;
                        } 
                        return getForm(val);
                    },
                    getLastForm: function() {
                        return getForm();
                    },
                    getFirstForm: function() {
                        first();
                        return getCurrentForm();
                    },
                    addForm: function() {
                        return addForm();
                    },
                    addNForms: function(n) {
                        return addNForms(n);
                    },
                    // Number of active forms
                    getFormsCount: function() {
                        return getFormsCount();
                    },
                    hasForms: function() {
                        return hasForms();
                    },
                    canAddForm: function() {
                        return canAddForm();
                    },
                    canRemoveAllForms: function() {
                        return canRemoveAllForms();
                    },
                    // Can remove a form?
                    canRemoveForm: function() {
                        return canRemoveForm();
                    },
                    removeAllForms: function() {
                        return removeAllForms();
                    },
                    removeLastForm: function() {
                        return removeLastForm();
                    },
                    removeFirstForm: function() {
                        first();
                        return removeForm(getCurrentForm());
                    },
                    removeForm: function(val) {
                        if (typeof(val) != 'undefined') {
                            val++;
                        }
                        return removeForm(getForm(val));
                    },

                    /* ----- Advanced ----- */
                    inject: function(data) {
                        
                        // Loop over each data using a Proxy (function , context)
                        $.each(data, $.proxy( fillData, source ));
                    }
                    
            });

        }

        /**
         * Extends cloned forms with many useful methods,
         * used to control each form with javascript
         */
        function extendForm(form) 
        {
            // API
            $.extend( form, {
                setLabel: function(newLabel) {
                     return setLabelForForm(form, newLabel);
                },
                getLabel: function() {
                    return getLabelForForm(form);
                },
                inject: function(data) {
                    fillForm(form, data);
                },
                getNestedForms: function() {
                    return form.data('nestedForms');
                },
                getNestedForm: function(val) {
                    return form.data('nestedForms')[val];
                },
                getPosition: function() {
                    return getPositionForIndex(form.data('formIndex'));
                },
                getPreviousForm: function()
                {
                    return getPreviousForm(form);
                },
                getNextForm: function()
                {
                   return getNextForm(form);
                },
                removeForm: function()
                {
                   return removeForm(form);
                }
            });
        }

        /**
         * Normalize options
         */
        function normalizeOptions(options)
        {
            // Normalize limits options
            if (options.maxFormsCount > 0) {
                if (options.maxFormsCount < options.minFormsCount) {
                    options.maxFormsCount = options.minFormsCount;
                }
                if (options.iniFormsCount < options.minFormsCount || options.iniFormsCount > options.maxFormsCount) {
                    options.iniFormsCount = options.minFormsCount;
                }
            } else {
                if (options.iniFormsCount < options.minFormsCount) {
                    options.iniFormsCount = options.minFormsCount;
                }
            }

            if (!canRemoveAllForms()) {
                options.allowRemoveAll = false;
            }
        }


        /**
         * Gets the first element of the collection and decorates with jquery
         */
        var source = $(this).first();
        
        // Extend source with useful methods
        extendSource(source);

        var add,
            addN,
            addNInput,
            addNButton,
            removeLast,
            removeCurrent,
            removeAll,
            controls,
            template,
            templateForm,
            noFormsTemplate,
            formFields = "input, checkbox, select, textarea",
            forms = [],
            ip =  false, // Internal ip
            // Default options
            defaults = {
                
                // Controls selectors
                addSelector: '#' + $(this).attr("id") + '_add',
                addNSelector: '#' + $(this).attr("id") + '_add_n',
                addNInputSelector: '#' + $(this).attr("id") + '_add_n_input',
                addNButtonSelector: '#' + $(this).attr("id") + '_add_n_button',
                removeLastSelector: '#' + $(this).attr("id") + '_remove_last',
                removeCurrentSelector: '#' + $(this).attr("id") + '_remove_current',
                removeAllSelector: '#' + $(this).attr("id") + '_remove_all',
                controlsSelector: '#' + $(this).attr("id") + '_controls',
                labelSelector: '#' + $(this).attr("id") + '_label',

                // Controls options
                allowRemoveLast: true,
                allowRemoveCurrent: true,
                allowRemoveAll: false,
                allowAdd: true,
                allowAddN: false,

                // Confirmations
                removeLastConfirmation: false,
                removeCurrentConfirmation: false,
                removeAllConfirmation: true,
                removeLastConfirmationMsg: 'Are you sure?',
                removeCurrentConfirmationMsg: 'Are you sure?',
                removeAllConfirmationMsg: 'Are you sure?',

                // Templates
                formTemplateSelector: '#' + $(this).attr("id") + '_template',
                noFormsTemplateSelector: '#' + $(this).attr("id") + '_noforms_template',
                separator: '<div style="width:100%; border-top:1px solid #ff0000; margin: 10px 0px;"></div>',

            // Limits
            iniFormsCount: 1,
            maxFormsCount: 20, // 0 = no limit
            minFormsCount: 1,
            incrementCount: 1 , // add N forms at one time
            noFormsMsg: 'No forms to display',

            // Id and names management
            indexFormat:'#index#',

            // Advanced options
            data: [], // A JSON based representation of the data which will prefill the form (equivalent of the inject method)
            pregeneratedForms: [],
            nestedForms: [],
            isNestedForm: false,
            parentForm: {},
            beforeClone: function() {},
            afterClone: function() {},
            beforeAdd: function() {},
            afterAdd: function() {},
            afterFill: function() {},
            afterRemoveCurrent: function(){},
            beforeRemoveCurrent: function(){},
            insertNewForms: 'after',
            continuousIndex: true //Keep index continuous and starting from 0 
        };


        setOptions(options);
        initialize();

        return source;
    };

    /**
     * JQuery original clone method decorated in order to fix an IE < 8 issue
     * where attributs especially name are not copied
     */
    jQuery.fn.cloneWithAttribut = function( withDataAndEvents ){
        if ( jQuery.support.noCloneEvent ){
            return $(this).clone(withDataAndEvents);
        }else{
            $(this).find("*").each(function(){
                $(this).data("name", $(this).attr("name"));
            });
            var clone = $(this).clone(withDataAndEvents);

            clone.find("*").each(function(){
                $(this).attr("name", $(this).data("name"));
            });

            return clone;
        }
    };

})(jQuery);
