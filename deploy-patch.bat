@echo off
rem FM Wholesale ERP - make an update zip for the live server
rem   deploy-patch.bat init   first time only: mark the current commit as live
rem   deploy-patch.bat        build deploy\patch-<date>.zip with only the changed files
rem   deploy-patch.bat done   after uploading: mark the current commit as live
cd /d "%~dp0"
php tools\deploy-patch.php %1
echo.
rem Keep the window open when double-clicked (full path: Git Bash has its own "find")
echo %cmdcmdline% | "%SystemRoot%\System32\find.exe" /i "/c" >nul && pause
