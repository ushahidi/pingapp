Ping Design Guide
============

PING'S UI is based on the all inclusive, crossbrowser, front-end framework [Foundation](http://foundation.zurb.com/docs/index.html) and utilizes [Sass](http://example.net/), which compiles to CSS via [Compass](http://example.net/). Foundation provides Sass mixins, which are an intergral part of PING'S grid and design.

### How to get started?

* `git clone https://github.com/ushahidi/pingapp.git` to local directory

* Follow [these directions](http://foundation.zurb.com/docs/sass.html) to install Foundation and Compass on your machine. *NOTE: Both Ruby and RubyGems Library are required before installation*.
 
* HTML/PHP markup files are located in `application/views`

* SASS files are located in `httpdocs/media/scss`

#####To make style changes … *Do not edit the `.css` files directly*  

* In your terminal navigate to the `media` folder and run `compass watch` (this will compile all `.scss` files into `css/app.css` upon save)

* Then make all style changes within the individual `.scss.` files, creating new ones and importing them into `app.scss` as needed

* Now when you save your changes, your `.scss` files will compile to `css/app.css` and changes will take effect upon browser refresh


####***Important!!***
 
PING is a responsive, device agnostic app, meaning it is flexible and optimized for usability regardless of the device. It looks good on desktop, mobile phones and everything in between.  

It is important to know that the PING grid is semantic, meaning it is controlled via the `.scss` files, **not the HTML**. If you need to adjust or add to the grid, please do so via [Sass mixins provided by Foundation](http://foundation.zurb.com/docs/components/grid.html). Keep the grid out of the markup and in the CSS.

When in doubt reference the existing code and the Foundation docs.

***If you have any questions feel free to contact seth(at)ushahidi.com***
