module.exports = function(grunt) {

    grunt.initConfig({
        jst: {
            compile: {
                options: {
                    templateSettings: {
                        variable: 'rc'
                    },
                    processName: function(filepath) {
                        return filepath.substr(filepath.lastIndexOf('/') + 1).replace('.tpl', '');
                    }
                },
                files: {
                    "../web/assets/js/templates.js": ["../web/assets/templates/**/*.tpl"]
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-jst');
};