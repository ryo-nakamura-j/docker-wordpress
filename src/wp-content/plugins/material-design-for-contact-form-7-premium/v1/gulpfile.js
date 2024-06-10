var gulp = require('gulp'),
	sass = require('gulp-sass'),
	autoprefixer = require('gulp-autoprefixer'),
	notify = require('gulp-notify'),
	cache = require('gulp-cache'),
    postcss = require('gulp-postcss'),
	prefixSelector = require('postcss-prefix-selector');

// Styles
gulp.task('styles', function() {
	var postcssTasks = [
        prefixSelector({
			prefix: '#cf7md-form ',
			transform: function (prefix, selector, prefixAndSelector) {
				if (
					selector.indexOf('.mdc-theme--dark') === 0 || 
					selector.indexOf('.mdc-theme--light')  === 0 || 
					selector.indexOf('.cf7md-form') === 0 ||
					selector.indexOf('.cf7md-spacing') === 0
				) {
					return prefix.trim() + selector;
				} else {
					return prefixAndSelector
				}
			}
		})
    ];
	return gulp.src('scss/cf7-material-design.scss')
		.pipe(sass({
			style: 'compressed',
            includePaths: ['./node_modules']
		}))
		.pipe(postcss(postcssTasks))
		.pipe(autoprefixer({
			browsers: ['last 2 version'],
			grid: false
		}))
		.pipe(gulp.dest('assets/css'))
		.pipe(notify({ message: 'Compiled CSS' }));
});

// Admin Styles
gulp.task('adminstyles', function() {
	return gulp.src('scss/cf7-material-design-admin.scss')
		.pipe(sass({
			style: 'compressed',
            includePaths: ['./node_modules']
		}))
		.pipe(autoprefixer({
			browsers: ['last 2 version'],
			grid: false
		}))
		.pipe(gulp.dest('assets/css'))
		.pipe(notify({ message: 'Compiled Admin CSS' }));
});

// Default task
gulp.task('default', ['styles', 'adminstyles', 'watch']);

// Watch files
gulp.task('watch', function() {

	// Watch .scss files
	gulp.watch('scss/**/*.scss', ['styles', 'adminstyles']);

});