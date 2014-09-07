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
          'themes/allmybooks-2014/javascripts/site.min.js': ['themes/allmybooks-2014/javascripts/html.sortable.js', 'themes/allmybooks-2014/javascripts/site.js']
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
        tasks: ['compass:dist']
      },
      js: {
        files: ['themes/allmybooks-2014/javascripts/**/*.js'],
        tasks: ['jshint:all', 'uglify:min']
      }
    },
    browserSync: {
      dev: {
        bsFiles: {
          src : 'themes/allmybooks-2014/*.css'
        },
        options: {
          proxy: "allmybooks.dev",
          watchTask: true // < VERY important
        }
      }
    }
  });
  
  // Load the plugin that provides the task.
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-browser-sync');
  
  // Default task(s).
  grunt.registerTask('default', ['browserSync', 'watch']);
};