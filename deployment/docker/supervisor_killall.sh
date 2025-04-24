#!/bin/sh

# This script kills the supervisord process when any child process fails
# It's used as an eventlistener in supervisord.conf

# Read the stdin from supervisord
read -r line

# Exit with non-zero code to bring down the container
echo "Process failed, shutting down container: $line"
exit 1