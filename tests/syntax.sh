#! /usr/bin/env bash

echo "Testing PHP syntax..."
set -o pipefail

for file in $(find . -name '*.php');
do
	php -l "$file" | sed 's/^/        /' || { echo 'Syntax check failed!'; exit 1; }
done