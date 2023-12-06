# EQ2EMu-dbeditors

The first public release of the eq2emu database editor. 

Code originally written by: JohnAdams (https://mmoemulators.com/).

Maintained by\
TheFoof ([Lead maintainer](https://github.com/theFoof))\
Cynnar ([maintainer](https://github.com/cynnar))\
zarobhr/EmemJR ([maintainer](https://github.com/zarobhr))\
Splashsky ([maintainer](https://github.com/splashsky))\
Txmcse ([maintainer](https://github.com/txmcse))\
Ace.JS team ([code editor](https://ace.c9.io/))\
SOE, Daybreak ([artwork](https://www.daybreakgames.com/home))\
And more over the years.

## Explanation of the Different Editors.
EQ2DB is the primary editor. It currently has the most working features and is under active development.\
EQ2DB2 is sort of V1 of JohnAdams's editor. The names are a bit confusing.

# *** NOTES ***

This code is/was for learning purposes, it is messy, ugly, and outdated. MANY people have worked on it over the years so coding styles are all over the place.

There are MANY hardcoded values, and if running a PHP version greater than the php7.x series you will have to make further tweaks.\
This code is provided as-is. You are responsible for securing your systems.\
This is not for general users!!\
This is intended for users familiar with PHP and who wish to contribute.

Currently, there is an effort to move all the needed bits from eq2db2 to eq2db, which is the most updated version.

Support for this will be very limited to if/when any of the current developers/admins have time to answer questions.

### Files of note:
EQ2DB->.env.example  --  Must be edited and renamed .env\
EQ2DB->eq2editor.sql --  Must be merged into your MYSQL/MariaDB server.\
EQ2DB2->common->.config.php -- Must be edited and renamed config.php.
