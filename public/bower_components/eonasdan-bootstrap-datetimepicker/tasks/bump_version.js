module.exports = function (grunt) {
    grunt.registerTask('bump_version', function (version) {
        if (!version || version.split('.').length !== 3) {
            grunt.fail.fatal('malformed version. Use\n\n    grunt bump_version:1.2.3');
        }

        grunt.config('string-replace.bootstrap-datetimepicker-js', {
            files: {'src/js/bootstrap-datetimepicker.js': 'src/js/bootstrap-datetimepicker.js'},
            options: {
                replacements: [
                    {
                        pattern: /\/*! version : .*/,
                        replacement: '! version : ' + version
                    }
                ]
            }
        });

        grunt.config('string-replace.bootstrap-datetimepicker-css', {
            files: { 'src/less/_bootstrap-datetimepicker.less': 'src/less/_bootstrap-datetimepicker.less' },
            options: {
                replacements: [
                    {
                        pattern:     / * version : .*/,
                        replacement: ' version : ' + version
                    }
                ]
            }
        });

        grunt.config('string-replace.package-json', {
            files: {'package.json': 'package.json'},
            options: {
                replacements: [
                    {
                        pattern:     /"version": .*/,
                        replacement: '"version": "' + version + '"'
                    }
                ]
            }
        });

        grunt.config('string-replace.bower-json', {
            files: {'bower.json': 'bower.json'},
            options: {
                replacements: [
                    {
                        pattern:     /"version": .*/,
                        replacement: '"version": "' + version + '",'
                    }
                ]
            }
        });

        grunt.config('string-replace.component-json', {
            files: {'component.json': 'component.json'},
            options: {
                replacements: [
                    {
                        pattern:     /"version": .*/,
                        replacement: '"version": "' + version + '",'
                    }
                ]
            }
        });

        grunt.config('string-replace.composer-json', {
            files: {'composer.json': 'composer.json'},
            options: {
                replacements: [
                    {
                        pattern:     /"version": .*/,
                        replacement: '"version": "' + version + '",'
                    }
                ]
            }
        });

        grunt.task.run([
            'string-replace:bootstrap-datetimepicker-js',
            'string-replace:bootstrap-datetimepicker-css',
            'string-replace:package-json',
            'string-replace:bower-json',
            'string-replace:component-json',
            'string-replace:composer-json'
        ]);
    });
};
