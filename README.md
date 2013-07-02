wildfire.cms_static_cache
=========================

Creation:
- Hooks in to "cms.save.success.finished" event, checks class of object is the same as the content model
- Looks to check there are no rules excluding the item-
- Adds readonly=1 argument to the url being used
- Uses all attached urls as paths to save the pages by checking the URL_MAP_MODEL
- Content from the url is then saved to file pathes matching those urls & the URL_MAP_MODEL is updated with the file path & time
- base path is tmp/cache/static/PATH_BASED_ON_URL/index.html

To Use:
- Create a symlink from tmp/cache/statics to public/statics (ln -s tmp/cache/statics/ public/statics)
- Amend  Apache / Nginx config files, see apache.example.htaccess & nginx.example.conf
- the nginx / apache files are work in progress and may need further editing





