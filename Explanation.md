## Problem to be solved
A WordPress plugin that crawls the homepage for the links being used. The crawling should happen every hour and a manual button to crawl it.

It should show the crawled results on the admin page and also save the crawled result in the database, generate a sitemap.html file, and generate HTML version of the homepage. 

Furthermore, it should clean up previous results in the database, delete previous files, and regenerate them.

Moreover, provide the option to allow visitors to view sitemap.html

## Technical specifications
An admin setting subpage should be added first under Settings on admin using `add_submenu_page`. On the page, a button should be provided for manual crawling that fits with the default WordPress UI inside the `<div class="wrap">` container.

Write a custom function `display_linked_pages` to handle the crawling of the homepage URL. Using the `DOMDocument` class, we can read and scrap the anchors being used in the homepage.

This crawling function should fire once the button is clicked. For that when the button is clicked, an Ajax request is fired hooked to `wp_ajax_crawl_homepage` and `wp_ajax_nopriv_crawl_homepage`. Then this event is handled in `explorer.js` which handles the click and shows the results in `div` with ID: `seo-link-explorer__results`

Now for running the crawling every hour, the function `display_linked_pages` should be fired with the custom event hook: `seo_link_explorer_event`

## Technical decisions
For the crawling part, I preferred using the `DOMDocument` instead of the `file_get_contents` function since it's easier to get the HTML version of the page with its `saveHTML` function.

Regarding showing the crawled result, I preferred to go with the AJAX call since I have seen most crawling plugins do it like that for a better user experience.
Initially, I handled all the functionality in one class to make it work. In the later development phase, chunked the class into multiple that handle specific functionality.

Decided to save the data to the option table since it has only two data to be saved. The linked pages data is saved as full `ul` structure since the same structure is being in multiples instances.

The generated files are saved in uploads folder in its custom plugin folder that is publicly accessible.

Regarding the CRON part, I tested the event with `wp cli event run`

## Code workflow
Intially when the plugin is activated, the button to crawl the homepage is shown. Once it is clicked, an AJAX request is fired that
- Crawls the homepage
- Saves the retrieved links to the database's option table
- Retrieves and shows the results on the page- Generate the sitemap.html and homepage.html with the current timestamp
- Save the generated files in `uploads/seo-link-explorer` folder
- Saves the generated sitemap's filename to the database to be used later
- Update the sitemap's URL

On subsequent click of the button,
- Crawls the latest homepage
- Delete the previous linked pages' data in the database
- Update the database with new crawled data
- Delete previously created files- Regenerate the files
- Save the generated files in `uploads/seo-link-explorer` folder
- Update the sitemap's URL

Firstly the `Init` function is included in the plugin's core file. This class is initialized in its own file `Init.php`

Then in `Init`'s `init_plugin` function, all the other classes are instanced.
First, the `Setting` class adds the subpage and its content like button and results.
Then the `Event` class adds the AJAX calls and the crawling functions which are run on AJAX calls and on the CRON job.

The `File` class provides helper functions like `save_sitemap_html`, `save_page_html`, and `get_sitemap_url` to be used on the `Event` class.

Then the `Cron` class adds a custom schedule and event to fire the crawling on every hour.

Shortcode provides the shortcode: `[seo_link_explorer_sitemap_link]` to show the sitemap URL on the front end.

`Init` - Handles the initialization

`Setting` - Add subpage and admin interface

`Event` - Handles the crawling Ajax event and saves it to the database

`File` - Provides functions for file deletion and generation

`Cron` - Handles the cron job

`Shortcode` - Provides shortcode to show sitemap URL

## Conclusion
**SEO Link Explorer** crawls/explorer your homepage's links. With an automatic hourly crawl feature and a button for manual initiation, it ensures that the homepage is properly crawled.

It displays the crawl findings on the admin page, but it also stores the crawl results in the database. It generates a sitemap.html file and generates an HTML version of your homepage. To maintain a cleanup, it tidies up previous database results and deletes outdated files before regenerating them. Lastly, it provides visitors the privilege to view the generated sitemap.html.


