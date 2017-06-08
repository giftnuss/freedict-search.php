#!/bin/bash

set -x
set -e

(cd public ; php -S 127.0.0.1:7200 index.php )
