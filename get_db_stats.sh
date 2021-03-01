#!/bin/sh

CNT_TOTAL=$(mysql links -s --skip-column-names  -e "select count(*) from links")
CNT_EMPTY_TAGS=$(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE tags = ''")
CNT_NULL_TAGS=$(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE tags IS NULL")
CNT_ZERO_STATUS=$(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE status = 0")
CNT_NULL_STATUS=$(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE status IS NULL")
CNT_EMPTY_DESCRIPTION=$(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE description = ''")
CNT_NULL_DESCRIPTION=$(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE description IS NULL")

cat <<EOB >stats.json
{
  "total":${CNT_TOTAL},
  "emptytags":${CNT_EMPTY_TAGS},
  "nulltags":${CNT_NULL_TAGS},
  "zerostatus": ${CNT_ZERO_STATUS},
  "nullstatus": ${CNT_NULL_STATUS},
  "emptydescr":${CNT_EMPTY_DESCRIPTION},
  "nulldescr":${CNT_NULL_DESCRIPTION},
  "level1": $(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE tags = 'level1'"),
  "level2": $(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE tags = 'level2'"),
  "level3": $(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE tags = 'level3'"),
  "level4": $(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE tags = 'level4'"),
  "level5": $(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE tags = 'level5'"),
  "levelx": $(mysql links -s --skip-column-names  -e "SELECT COUNT(*) FROM links WHERE tags LIKE 'level%'")
}
EOB

cat stats.json

exit

mysql links -s --skip-column-names  -e "SELECT link FROM links LIMIT 1000" | cut -d "/" -f 2 | sort -u

mysql links -s --skip-column-names  -e "SELECT link FROM links WHERE SUBSTR(link,0,3) != 'http'" | cut -d "/" -f 2 | sort -u
