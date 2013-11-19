Ping Design Guide
============

PING'S UI is based on the all inclusive, cross-browser, front-end framework [Foundation](http://foundation.zurb.com/docs/index.html) and utilizes [Sass](http://example.net/), which compiles to CSS via [Compass](http://example.net/). Foundation provides Sass mixins, which are an integral part of PING'S grid and design.

### How to get started?

* `git clone https://github.com/ushahidi/pingapp.git` to local directory

* Follow [these directions](http://foundation.zurb.com/docs/sass.html) to install Foundation and Compass on your machine. *NOTE: Both Ruby and RubyGems Library are required before installation*.
 
* HTML/PHP markup files are located in `application/views`

* SASS files are located in `httpdocs/media/scss`

#####To make style changes … *Do not edit the `.css` files directly*  

* In your terminal navigate to the `media` folder and run `compass watch` (this will compile all `.scss` files into `css/app.css` upon save)

* Then make all style changes within the individual `.scss.` files, creating new ones and importing them into `app.scss` as needed

* Now when you save your changes, your `.scss` files will compile to `css/app.css` and changes will take effect upon browser refresh

### Semantic HTML & SASS Mixins

The PING code base uses semantic HTML, [Sass mixins provided by Foundation](http://foundation.zurb.com/docs/components/grid.html) and custom [Sass placeholders](http://sass-lang.com/documentation/file.SASS_REFERENCE.html#placeholder_selectors_).  

>#####Semantic HTML is defined as:
>*1. the use of HTML markup to reinforce the semantics, or meaning, of the information in web pages rather than merely to define its presentation or look*

####Example - tab:

So, instead of a presentational class  
`<div class="tabs">`  

we would use this meaningful, semantic, descriptive class  
`<div class="message-stats-tabs">`

and then style that semantic class using a Sass `@mixin` provided by Foundation ***and*** a custom PING `%placeholder`


*(this custom PING placeholder contains a Foundation Sass mixin. It is located in `_helpers.scss`)*

```
 %tabs-container {
 	@include section-container($section-type: tabs);
 	& > section {
 		@include section($section-type:tabs); 
 	}
}
```
Then we style our semantic class by extending the placeholder

```
.message-stats-tabs {
  @extend %tabs-container
}

```

####Example - button:

`<div class="small radius alert button"`  (too many presentational classes)  

`<div class="group-delete-button"`  (one meaningful, semantic class)  


Then we style our semantic class with a Sass mixin

```
.group-delete-button {
  @include button($padding: $button-sml, $bg: $alert-color, $radius: true);
}

```

*** Please reference the [Foundation Docs](http://foundation.zurb.com/docs/) and the existing code base as needed ***


####Example - grid:

The semanitic markup applies to the grid as well. If you need to adjust or add to the grid, please markup with semantic classes and style with [Sass mixins provided by Foundation](http://foundation.zurb.com/docs/components/grid.html).



```
 <div class="new-name-row">
	<div class="new-name-first">
		//content here
	</div>
 </div>
```

```
.new-name-row {
  @include grid-row();
}

.new-name-first {
  @include grid-column(12)
}
```

*** Please reference the [Foundation Docs](http://foundation.zurb.com/docs/) and the existing code base for more information ***

***If you have any questions feel free to contact Seth Hall*** 

* middle8media on Skype
* seth(at)ushahidi.com
