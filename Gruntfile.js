/**
 * Grunt
 *
 * @see http://gruntjs.com/api/grunt to learn more about how grunt works
 * @since  1.0
 */

module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		watch: {
			options: {
				livereload: true,
			},
			css: {
				files: ['css/source/*.css'],
				tasks: ['autoprefixer', 'cssmin'],
				options: {
					livereload: true
				},
			},
			js: {
				files: ['js/source/*.js'],
				tasks: ['uglify'],
				options: {
					livereload: true
				},
			},
			concat: {
				files: ['library/source/*','includes/deprecated/source/*'],
				tasks: ['concat'],
			},
			livereload: {
				// reload page when css, js, images or php files changed
				files: ['css/*.css', 'js/*.js', 'img/**/*.{png,jpg,jpeg,gif,webp,svg}', '**/*.php']
			},
		},

		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
			},
			my_target: {
				files: {
					'js/<%= pkg.name %>.min.js': ['js/source/*.js']
				}
			}
		},

		autoprefixer: {
			options: {
				browsers: ['last 2 versions']
			},
			multiple_files: {
                expand: true,
                flatten: true,
                src: 'css/source/*.css',
                dest: 'css/source/build/'
            }
		},

		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1
			},
			target: {
				files: {
					'css/<%= pkg.name %>.min.css': ['css/source/*.css']
				}
			}
		},

		concat: {
			options: {
				separator: '',
			},
			library: {
				src: ['library/source/*'],
				dest: 'library/premise-library.php',
			},
			deprecated: {
				src: ['includes/deprecated/source/*'],
				dest: 'includes/deprecated/deprecated.php',
			},
		},

		phpdocumentor: {
	        dist: {
	            options: {
	                directory : './',
	                target : 'documentation/phpdoc',
	                template: 'responsive-twig'
	            }
	        }
	    },
		
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-concat');
	// create PHP documentation
	grunt.loadNpmTasks('grunt-phpdocumentor');
	// Default task(s).
	grunt.registerTask( 'default', ['watch'] );

};