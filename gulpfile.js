/* Require all npm modules
 ———————————————————————————————————————— */
var gulp                = require('gulp'),
    autoprefixer        = require('autoprefixer'),
    clean               = require('gulp-clean'),
    concat              = require('gulp-concat'),
    cssNano             = require('gulp-cssnano'),
    fileInclude         = require('gulp-file-include'),
    imagemin            = require('gulp-imagemin'),
    modernizr           = require('gulp-modernizr'),
    newer               = require('gulp-newer'),
    plumber             = require('gulp-plumber'),
    postcss             = require('gulp-postcss'),
    rename              = require('gulp-rename'),
    sourcemaps          = require('gulp-sourcemaps'),
    sequence            = require('run-sequence'),
    uglify              = require('gulp-uglify'),
    pngquant            = require('imagemin-pngquant'),
    lost                = require('lost'),
    cssCalc             = require('postcss-calc'),
    cssColorFunction    = require('postcss-color-function'),
    cssConditionals     = require('postcss-conditionals'),
    cssCustomMedia      = require('postcss-custom-media'),
    cssDiscardComments  = require('postcss-discard-comments'),
    cssImport           = require('postcss-import'),
    cssMixins           = require('postcss-mixins'),
    cssNested           = require('postcss-nested'),
    cssSimpleExtend     = require('postcss-simple-extend'),
    cssSimpleVars       = require('postcss-simple-vars'),
    browserSync         = require('browser-sync'),
    config              = require('./config'),
    reload              = browserSync.reload;

/* Store directory paths
 ———————————————————————————————————————— */
// src directories
var srcPath = {
    root:       'resources/assets/src/',
    bower:      'bower_components/',
    styles:     'resources/assets/src/css/',
    scripts:    'resources/assets/src/js/',
    images:     'resources/assets/src/images/',
    fonts:      'resources/assets/src/fonts/',
    partials:   'resources/assets/src/html-partials/'
};
// dist directories
var distPath = {
    root:       'resources/assets/dist/',
    styles:     'resources/assets/dist/css/',
    scripts:    'resources/assets/dist/js/',
    fonts:      'resources/assets/dist/fonts/',
    images:     'resources/assets/dist/images/'
};

var publicPath = {
    root:       'public/package/ensphere/ensphere/',
    styles:     'public/package/ensphere/ensphere/css/',
    scripts:    'public/package/ensphere/ensphere/js/',
    fonts:      'public/package/ensphere/ensphere/fonts/',
    images:     'public/package/ensphere/ensphere/images/'
};

/* Move jQuery fallback from src to dist
 ———————————————————————————————————————— */
gulp.task('moveJquery', function()
{
    return gulp.src( srcPath.scripts + 'vendor/jquery-3.1.1.min.js' )
        .pipe(gulp.dest(distPath.scripts))
});

/* Clean dist directories pre-compile
 ———————————————————————————————————————— */
gulp.task('clean', function(cb)
{
    return gulp.src([
        distPath.styles + 'css',
        distPath.scripts + 'js',
        publicPath.styles + 'css',
        publicPath.scripts + 'js',
        publicPath.images + 'images',
        publicPath.images + 'images'
    ], { read: false } )
        .pipe( clean({ force : true }) );
});

/* Process stylesheets
 ———————————————————————————————————————— */
gulp.task('styles:dev', function()
{
    // Store PostCSS processors
    var processors = [
        cssImport(),
        cssCalc(),
        cssMixins(),
        cssSimpleVars({
            silent: true
        }),
        cssColorFunction(),
        cssNested(),
        cssCustomMedia(),
        lost(),
        cssConditionals(),
        cssSimpleExtend(),
        cssDiscardComments(),
        autoprefixer({
            browsers: [
                'last 2 versions',
                'safari 5',
                'ie 9',
                'ie 10',
                'ie 11'
            ]
        })
    ];
    // Return source css files
    return gulp.src(srcPath.styles + 'ensphere.pcss')
    // Return any errors
        .pipe(plumber({
            errorHandler: function (err) {
                console.log(err);
                this.emit('end');
            }
        }))
        // Initialize sourcemap
        .pipe(sourcemaps.init())
        // Process through postcss
        .pipe(postcss(processors))
        .pipe(rename({
            basename : 'ensphere',
            extname: '.css'
        }))
        // Write the sourcemap
        .pipe(sourcemaps.write('maps', {
            includeContent: false,
            sourceRoot: distPath.styles + 'ensphere.css'
        }))
        // Write out the css file to dist
        .pipe(gulp.dest(distPath.styles));
});

// Main styles task which is triggered only when styles:dev has finished.
gulp.task('styles:main', ['styles:dev'], function() {
    // Return the styles.dev.css file in dist
    return gulp.src(distPath.styles + 'ensphere.css')
    // Minify the file
        .pipe(cssNano())
        // Duplicate file and add .min onto filename
        .pipe(rename({suffix: '.min'}))
        // Write out file back to dist
        .pipe(gulp.dest(distPath.styles))
        .pipe(rename({
            basename : 'app',
            suffix: ''
        }))
        .pipe(gulp.dest(publicPath.styles))
        .pipe( reload({ stream : true } ) );
});

gulp.task( 'styles', [ 'styles:main' ], function() {

    return gulp.src( distPath.styles + 'maps/ensphere.css.map' )
        .pipe( gulp.dest( publicPath.styles + 'maps' ) );

});

/* Process scripts
 ———————————————————————————————————————— */
gulp.task('scripts', function()
{
    // Return source js files
    return gulp.src([
        srcPath.scripts + 'vendor/*.js',
        srcPath.scripts + 'patterns/*.js',
        srcPath.scripts + 'ensphere.js'
    ])
    // Return any errors
        .pipe(plumber({
            errorHandler: function (err) {
                console.log(err);
                this.emit('end');
            }
        }))
        // Initialize sourcemap
        .pipe(sourcemaps.init())
        // Concatenate all the files into one
        .pipe(concat('ensphere.js'))
        // write the sourcemap
        .pipe(sourcemaps.write({
            includeContent: false,
            sourceRoot: publicPath.scripts
        }))
        // Write out the js file to dist
        .pipe(gulp.dest(distPath.scripts))
        // Duplicate file and add .min onto filename
        .pipe(rename({suffix: '.min'}))
        // Minify the file
        .pipe(uglify())
        // Write out file back to dist
        .pipe(gulp.dest(distPath.scripts))
        .pipe(rename({
            basename : 'all',
            suffix: ''
        }))
        .pipe(gulp.dest(publicPath.scripts))
        .pipe( reload({ stream : true } ) );
});

/* Create a custom modernizr file
 ———————————————————————————————————————— */
gulp.task('modernizr', function()
{
    // Return source js files
    return gulp.src(srcPath.scripts + '**/*.js')
    // Return any errors
        .pipe(plumber({
            errorHandler: function (err) {
                console.log(err);
                this.emit('end');
            }
        }))
        // Generate the custom modernizr file
        .pipe(modernizr('modernizr.min.js'))
        // Minify the file
        .pipe(uglify())
        // Write out file to dist
        .pipe(gulp.dest(distPath.scripts));
});

/* Process images
 ———————————————————————————————————————— */
gulp.task('images', function()
{
    return gulp.src(srcPath.images + '*')
        .pipe(newer(distPath.images))
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest(distPath.images))
        .pipe(gulp.dest(publicPath.images));
});

/* Process HTML and partials
 ———————————————————————————————————————— */
gulp.task('html', function()
{
    // Return all html files in src
    return gulp.src([
        srcPath.root + '*.html'
    ])
    // Process html partials
        .pipe(fileInclude({
            prefix: '@@',
            basepath: '@file'
        }))
        // Write out complied html to dist
        .pipe( gulp.dest(distPath.root));
});

/* Process Fonts
 ———————————————————————————————————————— */
gulp.task('fonts', function()
{
    // Return all font files in src
    return gulp.src([
        srcPath.fonts + '*'
    ])
    // Write out complied html to dist
        .pipe(gulp.dest(distPath.fonts))
        .pipe(gulp.dest(publicPath.fonts));
});

/* Configure Browser-sync for livereload
 ———————————————————————————————————————— */
gulp.task('browser-sync', function() {
    browserSync.init( null, {
        proxy: config.domain,
        notify: false
    });
});

/* Watch files and run tasks on change
 ———————————————————————————————————————— */
gulp.task('watch', [ 'browser-sync' ], function() {
    gulp.watch( srcPath.styles + '**/*.pcss', ['styles'] );
    gulp.watch( srcPath.root + '**/*.html', ['html'] );
    gulp.watch( srcPath.scripts + '**/*.js', ['scripts', 'modernizr'] );
    gulp.watch( srcPath.images + '**/*', ['images'] );
    gulp.watch( srcPath.fonts + '**/*', ['fonts'] );
});

/* Default gulp task
 ———————————————————————————————————————— */
gulp.task('default', ['clean'], function() {
    sequence( 'moveJquery', 'fonts', 'images', 'modernizr', 'styles', 'scripts' );
});
