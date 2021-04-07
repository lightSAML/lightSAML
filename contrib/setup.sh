#!/bin/sh

if [ ! -f php-cs-fixer.phar ]; then
    echo "The php-cs-fixer.phar is required... downloading..."
    wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O php-cs-fixer.phar || curl https://cs.symfony.com/download/php-cs-fixer-v2.phar -o php-cs-fixer.phar || { echo >&2 "I require wget or curl but they are not installed.  Aborting."; exit 1; }
fi

# Copy the pre-commit hook to the current repository hooks directory.
cp contrib/pre-commit .git/hooks/pre-commit

# Add execution permission for pre-commit file.
chmod +x .git/hooks/pre-commit