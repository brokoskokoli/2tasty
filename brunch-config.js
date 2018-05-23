'use strict';

exports.config = {
    paths: {
        'public': 'public',
        'watched': ['assets']
    },
    files: {
        javascripts: {
            joinTo: {
                'js/vendor.js': /^node_modules/,
                'js/app.js': /^assets\/js/,
            }
        },
        stylesheets: {
            joinTo: {
                'css/vendor.css': /^node_modules/,
                'css/app.css': /^assets\/scss/
            }
        }
    },
    conventions: {
        assets: /^assets\/static/
    },
    // Configure your plugins
    plugins: {
        sass: {
            options: {
                includePaths: [
                    "node_modules"
                ], // tell sass-brunch where to look for files to @import
            },
            precision: 8 // minimum precision required by bootstrap-sass
        },
    },
    watcher: {
        awaitWriteFinish: true
    },
    modules: {
        wrapper: (path, data) => {
        return ( path.indexOf('initialize') === -1 && path.indexOf('vendor') === -1 ) ? `
                require.define({'${path}': function(exports, require, module) {
                  ${data}
                }});\n\n
            ` : `${data}`
        },
        autoRequire: {
            'js/initialize.js': ['assets/js/initialize']
        }
    },

    npm: {
        enabled: true,
        globals: {
            $: 'jquery',
            jQuery: 'jquery',
            bootstrap: 'bootstrap',
            Bloodhound: 'bloodhound-js'
        }
    }
};
