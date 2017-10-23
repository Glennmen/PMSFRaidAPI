module.exports = function (grunt) {

    // load plugins as needed instead of up front
    require('jit-grunt')(grunt);
    require('phplint').gruntPlugin(grunt);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        phplint: {
            files: ['**.php', '**/*.php', '!node_modules/**']
        }
    });
    grunt.registerTask('lint', ['newer:phplint']);

};
