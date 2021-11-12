const mix = require('laravel-mix')
const glob = require('glob')
require('laravel-mix-purgecss')

const paths = {
    styles: {
        src: 'resources/assets/sass/',
        components: 'resources/assets/sass/partials/_components.scss'
    },
    scripts: {
        src: 'resources/assets/js/',
    },
    public: 'public/assets/'
}

mix
    .webpackConfig({ devtool: "inline-source-map" })
    .options({
        postCss: [
            require('postcss-discard-comments')({
                removeAll: true
            })
        ],
        uglify: {
            comments: false
        }
    })

//Compile all theme SCSS to public folder
glob.sync('!(*.example).scss', {cwd: paths.styles.src}).forEach((fileName,) => {
    let src = paths.styles.src+fileName
    let dest = paths.public+'application-'+fileName.replace('scss', 'css')
    mix
        .sass(src, dest, {
            sassOptions: {
                // Mute deprecation warnings because of Bootstrap 4
                quietDeps: true,
            }
        })
        .purgeCss()
})

// Compile all javascript
mix .js(paths.scripts.src+'application.js', paths.public)
    .extract()

// Enable sourcemap and versioning
mix .version()
