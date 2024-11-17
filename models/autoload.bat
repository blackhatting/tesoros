@echo off
del autoload.php
echo <?php >> autoload.php
for %%f in (*) do echo require_once('%%f'); >> autoload.php
echo ?> >> autoload.php


