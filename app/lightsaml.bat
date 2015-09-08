@echo off

if not exist "%PHPBIN%" if "%PHP_PEAR_PHP_BIN%" neq "" goto USE_PEAR_PATH
GOTO RUN
:USE_PEAR_PATH
set PHPBIN=%PHP_PEAR_PHP_BIN%
:RUN
if "%PHPBIN%" == "" set PHPBIN=php
if not exist "%PHPBIN%" set PHPBIN=php
"%PHPBIN%" "app\lightsaml" %*
