# Introduction

Educational app aka [YouTube]  
Simple single page web application that was created as an example of basic [PHP], [MySQL] and [Vue]/[Vuetify] usage  
Not ready for production

[YouTube]: https://youtube.com/

# Technologies

## Front end

 - [Vue] - Single-page application routing
 - [Vue-router] - Single-page application routing
 - [Vuetify] - Project scaffolding
 - [Vuex] - Large-scale state management

[Vue]: https://vuejs.org/
[Vue-router]: https://github.com/vuejs/vue-router
[Vuetify]: https://vuetifyjs.com/en/
[Vuex]: https://github.com/vuejs/vuex

## Back end

 - [PHP] - PHP: Hypertext Preprocessor
 - [ffmpeg] - A complete, cross-platform solution to record, convert and stream audio and video
 - [ffprob] - ffprobe gathers information from multimedia streams and prints it in human- and machine-readable fashion
 
[ffmpeg]: https://ffmpeg.org/
[ffprob]: https://ffmpeg.org/ffprobe.html

[PHP]: https://www.php.net/

## Environment

 - [xampp] - Apache + MariaDB + PHP + Perl
 - [Docker] - Docker: platform as a service

[xampp]: https://www.apachefriends.org/index.html
[Docker]: https://www.docker.com/

## Database

 - [MySQL]: - MySQL
 
 [MySQL]: https://www.mysql.com/

# Install and run

 - Install [Docker]
 - Run
```sh
./docker.sh
```
  - Open browser and goto http://localhost:8086/phpmyadmin/
  - Using "phpmyadmin" console import [mytube.sql]
  - Open http://localhost:8086/www/
  - Enjoy! :-)

[mytube.sql]: https://github.com/andrewpavlov/mtube/blob/master/mytube.sql