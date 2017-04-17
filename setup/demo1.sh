SITE=site
# local storage directory, works well for small sites and demos

# directory of mytake repository
MYTAKE=mytake
MYTAKEP=../
# document root of website
# directoory of MobileDetect
MOBDET=MobileDetect
MOBDETP=../
# directoory of php-markdown
MKDOWN=php-markdown
MKDOWNP=../

# local storage directory, works well for small sites and demos
BOG=$(pwd)/bog
# BOG=bog

# local storage directory, directory group owner (e.g. apache, httpd, webapp)
WEBGRP=blog

# CITE
# http://stackoverflow.com/questions/7069682/how-to-get-arguments-with-flags-in-bash-script
while test $# -gt 0; do
  case "$1" in
    -mb)
      shift
      if test $# -gt 0; then
        MOBDET=$1
        MOBDETP=
      else
        HELP="no -mb path specified"
        MTERR=$HELP
      fi
      shift
      ;;
    -md)
      shift
      if test $# -gt 0; then
        MKDOWN=$1
        MKDOWNP=
      else
        HELP="no -md path specified"
        MTERR=$HELP
      fi
      shift
      ;;
    -mt)
      shift
      if test $# -gt 0; then
        MYTAKE=$1
        MYTAKEP=
      else
        HELP="no -mt path specified"
        MTERR=$HELP
      fi
      shift
      ;;
    -bg)
      shift
      if test $# -gt 0; then
        BOG=$1
      fi
      shift
      ;;
    -wg)
      shift
      if test $# -gt 0; then
        WEBGRP=$1
      fi
      shift
      ;;
    *)
      break
      ;;
  esac
done

# user file upload directory
UPL=$(pwd)/$SITE/gfx-upload

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
  MTERR="$MOBDET directory not found"
  if [ ! -d $MOBDET ]
  then
    HELP="ak no $MOBDET"
  fi
fi

if [ ! -v HELP ]
then
  MTERR="$MKDOWN directory not found"
  if [ ! -d $MKDOWN ]
  then
    HELP="ak no $MKDOWN"
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
      if [ -d "$SITE/mytake" ]
      then
      rm -f $SITE/mytake
      fi
      ln -s $MYTAKEP$MYTAKE                 $SITE/mytake

      if [ -d "$SITE/mobile_detect" ]
      then
      rm -f $SITE/mobile_detect
      fi
      ln -s $MOBDETP$MOBDET                 $SITE/mobile_detect

      if [ -d "$SITE/markdown" ]
      then
      rm -f $SITE/markdown
      fi
      ln -s $MKDOWNP$MKDOWN                 $SITE/markdown

      # insert local storage directory, determined dynamically
      sed s:##CWDBOG##:$BOG: $MYTAKE/setup/config_sample.php >$SITE/config_sample.php0
      sed s:##FILUPL##:$UPL: $SITE/config_sample.php0 >$SITE/config.php
      rm -f $SITE/config_sample.php0
#     cp mytake/setup/config.php            $SITE

      cp mytake/setup/index.php             $SITE
      cp mytake/setup/util.php              $SITE
      cp mytake/setup/base.*                $SITE
#     cp mytake/setup/panel_home_static.php $SITE
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
    echo 
    echo "options"
    echo "-mt path     override mytake files path     (e.g. /public/mytake)"
    echo "-mb path     override path to Mobile-Detect (e.g. /public/Mobile-Detect)"
    echo "-md path     override path to php_markdon   (e.g. /public/php-markdown)"
    echo "-bg path     override private file store    (e.g. /public/bog)"
    echo "-wg group    group write file store         (e.g. apache)"
    echo
    echo "Example"
    echo "sh mytake/setup/demo1.sh -mb /public/Mobile-Detect -md /public/php-markdown"
else
    echo 
    echo "Done, now update URL and file paths in config.php!"
    echo "  \$actv_url ="
    echo "  \$data_dir = "
    echo "  \$file_dir = "

fi

echo 
echo "[ to reset: rm -fr "$BOG"; rm -fr "$SITE"/* ]"

