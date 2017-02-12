# directory of mytake repository
MYTAKE=mytake
# document root of website
SITE=site
# local storage directory, works well for small sites and demos
BOG=bog
# local storage directory, directory group owner (e.g. apache, httpd, webapp)
WEBGRP=blog

MTERR="mytake directory not found"
if [ ! -d "$MYTAKE" ]
then
    HELP="ak no mytake show"
fi
echo $HELP

if [ ! -v HELP ]
then
  MTERR="$SITE directory not found"
  if [ ! -d "$SITE" ]
  then
    HELP="ak no site"
  fi
fi

if [ ! -v HELP ]
then
  MTERR="Mobile-Detect directory not found"
  if [ ! -d "Mobile-Detect" ]
  then
    HELP="ak no Mobile-Detect"
  fi
fi

if [ ! -v HELP ]
then
  MTERR="php-markdown directory not found"
  if [ ! -d "php-markdown" ]
  then
    HELP="ak no php-markdown"
  fi
fi

if [ ! -v HELP ]
then
  MTERR="$BOG local storage directory already defined/exists"
  if ! [ -d "$BOG" ]
  then
      echo creating BOG
      mkdir $BOG
      chown -R .$WEBGRP $BOG
      chmod -R g+s $BOG
      touch $BOG/profiles.txt
      mkdir -p $BOG/users
  else
      HELP="show"
  fi
fi

if [ ! -v HELP ]
then
  MTERR="site directory not found"
  if [ -d "$SITE" ]
  then
      ln -s ../$MYTAKE                      $SITE/$MYTAKE
      ln -s ../Mobile-Detect                $SITE/mobile_detect
      ln -s ../php-markdown                 $SITE/markdown
      cp mytake/setup/config.php            $SITE
      cp mytake/setup/index.php             $SITE
      cp mytake/setup/util.php              $SITE
      cp mytake/setup/base.*                $SITE
      cp mytake/setup/panel_home_static.php $SITE
      mkdir -p $SITE/gfx-stock
      cp mytake/setup/gfx-stock/*           $SITE/gfx-stock
      mkdir -p $SITE/gfx-upload
      # this should be necessery, code should pull this from gfx-stock
      cp mytake/setup/gfx-stock/newuser_avatar.png $SITE/gfx-upload
      mkdir -p $SITE/panel
      cp mytake/setup/panel/*               $SITE/panel
      mkdir -p $SITE/email
      cp mytake/setup/email/*               $SITE/email
      mkdir -p $SITE/profile
      cp mytake/setup/profile/*             $SITE/profile
      cp mytake/setup/prof.php              $SITE
  else
      echo nope
      HELP="show"
  fi
fi

if [ -v HELP ]
then
    echo "Error: "$MTERR
    echo 
    echo "[ to reset: rm -fr bog; rm -fr site/* ]"
else
    echo "Done, now update URL and file paths in config.php!"
    echo 
    echo "[ to reset: rm -fr bog; rm -fr site/* ]"
fi

