module.exports = function(grunt) {
  // Project config
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    uglify: {
      my_target: {
        files: {
          'themes/allmybooks-2014/javascripts/site.min.js': ['themes/allmybooks-2014/javascripts/jquery.sortable.js', 'themes/allmybooks-2014/javascripts/site.js']
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
      sass: {
        files: ['themes/allmybooks-2014/sass/**/*.scss'],
        tasks: ['compass:dist']
      }
    }
  });
  
  // Load the plugin that provides the task.
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  
  // Default task(s).
  grunt.registerTask('default', ['uglify', 'watch']);
};