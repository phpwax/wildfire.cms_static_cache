wildfire.cms_static_cache
=========================

Creation:
- Hooks in to "cms.save.success.finished" event, checks class of object is the same as the content model
- Looks at stored setting for readonly mode
- Uses all attached urls as paths to save the pages
- Adds readonly=1 argument to the url being used
- result is then saved to matching file path
- base path is tmp/cache/static/PATH_BASED_ON_URL/index.html


To Use:
- Create a symlink from tmp/cache/statics to public/statics (ln -s tmp/cache/statics/ public/statics)
- Amend  Apache / Nginx config files, see apache.example.htaccess & nginx.example.conf





