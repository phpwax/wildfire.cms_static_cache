wildfire.cms_static_cache
=========================

Creation:
- Hooks in to "cms.save.success.finished" event, checks class of object is the same as the content model
- Looks at stored setting for readonly mode
- Uses all attached urls as paths to save the pages
- Adds readonly=1 argument to the url being used
- result is then saved to matching file path
- base path is tmp/cache/static/PATH_BASED_ON_URL/index.html

Apache:

Add this to your htaccess for apache servers (under the RewriteBase but before the current RewriteConditions):

RewriteCond statics/$1index.html -f
RewriteRule (.*) /statics/$1index.html [L,NC]

Nginx:



