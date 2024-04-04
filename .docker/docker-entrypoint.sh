#!/bin/sh

# check if the first argument passed in looks like a flag
if [ "${1#-}" != "$1" ]; then
  set -- /sbin/tini -- neoscs "$@"
# check if the first argument passed in is neoscs
elif [ "$1" = 'neoscs' ]; then
  set -- /sbin/tini -- "$@"
# check if the first argument passed in matches a known command
elif ["$1" = 'run' ]; then
  set -- /sbin/tini -- neoscs "$@"
fi

exec "$@"