# rtCamp Assignment: Twitter Album Challenge

Create a small PHP-script to accomplish following parts:

### Part-1: Album Slideshow

   1. User visits your script page
   2. User will be asked to connect using his FB account
   3. Once authenticated, your script will pull his album list from FB
   4. User will click on an album name/thumbnail
   5. A jQuery-slideshow will start showing photos in that album (in full-screen mode)

#### Part-2: Download Album

   1. Beside every album icon (step #4 in part-1), add a new icon/button saying “Download This Album”
   2. When the user clicks on that button, your script will fetch all photos in that album behind the scene and zip them inside a folder on server.
   3. You may start a “progress bar” as soon as user-click download button as download process may take time.
   4. Once zip building on server completes, show user link to zip file.
   5. When user clicks zip-file link, it will download zip folder without opening any new page.
   6. Beside album names, add a checkbox. Then add a common “Download Selected Album” button. This button will download selected albums into a common zip that will contain one folder for each album. Folder-name will be album-name.
   7. Also, add a big “Download All” button. This button will download all albums in a zip, similar to above.

### Part-3: Backup albums to Google Drive

   1. Provide the user with an option to move albums to a Google Drive.
       - The Google Drive will contain a master folder whose name will be of the format facebook_<username>_albums where username will be the Facebook username of the user.
       - The user’s Facebook albums will be backed up in this master folder. Photos from each album will go inside their respective folders. Folder names will be the same as the Facebook album names.
   2. To improve the user experience, include the three following buttons:
       - “Move” button- This button will appear under each album on your website. When clicked, the corresponding album only will be moved to Google Drive
       - “Move Selected”- This button will work along with a checkbox system. The user can select a few albums via checkboxes and click on this button. Only the selected albums will be moved to Google Drive
       - “Move All”- This button will immediately move all user albums to Google Drive within their respective folders.
   3. Make sure that the user is asked to connect to their Google account only once, no matter how many times they choose to move data.

# Demo
[Demo](https://rtcamp-facebook-album.herokuapp.com)


## Package
### Facebook PHP Graph SDK 
[Github Repo](https://github.com/facebook/php-graph-sdk)

Install the package using composer:
```
composer require facebook/graph-sdk
```

### Google APIs Client Library for PHP
[Github Repo](https://github.com/google/google-api-php-client)

Install the package using composer:
```
composer require google/apiclient:^2.0
```

### LightGallery
[Github Repo](https://github.com/sachinchoolur/lightGallery)

Install the package using composer:

```sh
$ bower install lightgallery --save
```
Or Install all modules together
``` sh
$ bower install lightgallery lg-thumbnail lg-autoplay lg-video lg-fullscreen lg-pager lg-zoom lg-hash lg-share
```

#### npm

find ```lightgallery``` on [npm](http://npmjs.org).

```sh
$ npm install lightgallery lg-thumbnail lg-autoplay lg-video lg-fullscreen lg-pager lg-zoom lg-hash lg-share
```

#### CDN
If you prefer to use a CDN you can load files via [jsdelivr](https://www.jsdelivr.com/projects/lightgallery) or [cdnjs](https://cdnjs.com/libraries/lightgallery)

Here is the [jsdelivr collection](https://cdn.jsdelivr.net/combine/npm/lightgallery,npm/lg-autoplay,npm/lg-fullscreen,npm/lg-hash,npm/lg-pager,npm/lg-share,npm/lg-thumbnail,npm/lg-video,npm/lg-zoom) of lightGallery and its modules.
