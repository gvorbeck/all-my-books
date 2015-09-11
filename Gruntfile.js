module.exports = function(grunt) {
  // Project config
  grunt.initConfig({
    // Read the package file
    pkg: grunt.file.readJSON('package.json'),
    // Spell out the tasks
    jshint: {
      all: ['Gruntfile.js', 'themes/allmybooks-2014/javascripts/site.js']
    },
    uglify: {
      min: {
        files: {
          'themes/allmybooks-2014/javascripts/site.min.js': [/*'themes/allmybooks-2014/javascripts/partials/html.sortable.js', 'themes/allmybooks-2014/javascripts/partials/jquery.sticky.js',*/ 'themes/allmybooks-2014/javascripts/partials/site.js']
        }
      }
    },
    compass: {
      dist: {
        options: {
          config: 'themes/allmybooks-2014/config.rb',
          force: true
        }
      }
    },
    watch: {
      css: {
        files: ['themes/allmybooks-2014/sass/**/*.scss'],
        tasks: ['compass:dist'],
        options: {
          livereload: true,
        }
      },
      js: {
        files: ['themes/allmybooks-2014/javascripts/partials/**/*.js'],
        tasks: ['jshint:all', 'uglify:min']
      }
    }
  });
  
  // Load the plugin that provides the task.
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  
  // Default task(s).
  grunt.registerTask('default', ['watch']);
};