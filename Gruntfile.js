/**
 *
 */
module.exports = function (grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		po2mo: {
			files: {
				src: 'languages/*.pot',
				expand: true
			}
		},
		
		pot: {
			options: {
				text_domain: 'woorule',
				dest: 'languages/',
				encoding: 'UTF-8',
				language: 'php',
				keywords: [ 
					'__:1',
					'_e:1',
					'_x:1,2c',
					'esc_html__:1',
					'esc_html_e:1',
					'esc_html_x:1,2c',
					'esc_attr__:1', 
					'esc_attr_e:1', 
					'esc_attr_x:1,2c', 
					'_ex:1,2c',
					'_n:1,2', 
					'_nx:1,2,4c',
					'_n_noop:1,2',
					'_nx_noop:1,2,3c'
				]
			},
			files: {
				src: [ '**/*.php' ],
				expand: true
			}
		}
	});

	grunt.loadTasks('tasks');

	grunt.loadNpmTasks('grunt-po2mo');
	grunt.loadNpmTasks('grunt-pot');
}

