#!/bin/sh

if [ ! -f php-cs-fixer.phar ]; then
    echo "The php-cs-fixer.phar is required... downloading..."
    wget http://get.sensiolabs.org/php-cs-fixer.phar -O php-cs-fixer.phar || curl http://get.sensiolabs.org/php-cs-fixer.phar -o php-cs-fixer.phar || { echo >&2 "I require wget or curl but they are not installed.  Aborting."; exit 1; }
fi

# Copy the pre-commit hook to the current repository hooks directory.
cp contrib/pre-commit .git/hooks/pre-commit

# Add execution permission for pre-commit file.
chmod +x .git/hooks/pre-commit