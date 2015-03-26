groundup 
=

##Theme Features:
* Extremely lightweight and fast framework (unstyled)
* File versioning for better caching
* Separate stylesheets for inlining critical CSS on the first pageload
* Load jQuery (when needed) from Google's CDN with a local fallback
* Adds mod_deflate to .htaccess
* Decluttered Admin
* Prevents WordPress from storing duplicate media.
* Renames additional media sizes according to the sizename (`file-thumbnail.jpg` instead of `file-320x640.jpg`)
* Automatic sitemap.xml and robots.txt creation

###Setup:
* Install node.js
* Clone the git repo into your themes folder - `git clone git://github.com/ascottmccauley/groundup.git`
* Update the node_modules - `npm update`
* Add Bower components - `bower update`
* Run Gulp to compile assets - `gulp`

###Building
#####sourcefiles
* `/src/` - Location for all of the precompiled assets. Gulp will work it's magic and put them in `/assets/`
* `/src/css` - All stylesheets from the css folder will be compiled and minified to `/assets/css`.
* - Bower dependencies must be imported directly into the stylesheet.
* - Child themes are not setup to enqueue the parent css (because of the url rewrite to `/assets/`), The parent SCSS files contain `$include-parent-classes: true !default;` to allow child themes to import the parent styles with `@import "../../../groundup/src/scss/main"`.
* `/src/js` - Gulp concatenates, and minifies js from Bower dependencies and the js folder along with a minified copy of jquery to `/assets/js`
* `/src/fonts` - all fonts from Bower dependencies and the fonts folder are copied to `/assets/fonts/`
* `src/img` - all images from Bower dependencies and the img folder are compressed and copied to `/assets/img`

#####functions
* `/includes/` - All of the ~~fun stuff~~ *functions*
* `/includes/helpers` - Helper functions used throughout the theme
* `/includes/setup` - Initial activation and theme setup options
* `/includes/cleanup` - Cleans up some out of the box WordPress behavior
* `/includes/admin` - Functions specific to the backend or admin_bar
* `/includes/scripts.php` - Loading all the assets [scripts, styles, and cookies]

#####templates
* `/templates` - Templates (surprised‽)
* `/templates/excerpt.php` - can be extended include the post_format or post_type
* `/templates/single.php` - can be extended include the post_format or post_type
* `/templates/meta.php` - time, tags, categories, comment count, image count, and exif info for a post 
* `/templates/comment.php` - Output for a single comment

###Conditions:
Feel free to use this, but be aware that I am not responsible for maintaining it and probably will not be available to answer any questions related to it.

MIT Open Source License
=======================

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
