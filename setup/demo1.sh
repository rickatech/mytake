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
  MTERR="$BOG local storage directory already defined/exists"
  if ! [ -d "$BOG" ]
  then
      echo creating BOG
      mkdir $BOG
      chown .$WEBGRP $BOG
      chmod g+s $BOG
      touch $BOG/profiles.txt
  else
      HELP="show"
  fi
fi

if [ ! -v HELP ]
then
  MTERR="site directory not found"
  if [ -d "$SITE" ]
  then
      cp mytake/setup/config.php site
      cp mytake/setup/index.php site
      cp mytake/setup/util.php site
      cp mytake/setup/panel_home_static.php site
  else
      echo nope
      HELP="show"
  fi
fi

if [ $HELP ]
then
    echo "Error: "$MTERR
fi

