@echo off
setlocal
chcp 65001 >nul

set PROGRAM=self-logger.bat
set DB_FILE=logger.db
set USERNAME=%USERNAME%
for /f "tokens=1-3 delims=. " %%a in ("%date%") do (
    set CURDATE=%%c-%%b-%%a
)
set CURTIME=%time:~0,8%
set DATETIME=%CURDATE% %CURTIME%

if not exist %DB_FILE% (
    sqlite3 %DB_FILE% "CREATE TABLE logs (id INTEGER PRIMARY KEY AUTOINCREMENT, user TEXT, datetime TEXT);"
)

sqlite3 %DB_FILE% "INSERT INTO logs (user, datetime) VALUES ('%USERNAME%', '%DATETIME%');"

echo Имя программы: %PROGRAM%
for /f "tokens=* usebackq" %%a in (`sqlite3 %DB_FILE% "SELECT COUNT(*) FROM logs;"`) do set COUNT=%%a
echo Количество запусков: %COUNT%

for /f "tokens=* usebackq" %%a in (`sqlite3 %DB_FILE% "SELECT datetime FROM logs ORDER BY id LIMIT 1;"`) do set FIRST=%%a
echo Первый запуск: %FIRST%

echo ---------------------------------------------
echo User      ^| Date
echo ---------------------------------------------
for /f "tokens=1,2* delims=|" %%a in ('sqlite3 %DB_FILE% "SELECT user, datetime FROM logs;"') do (
    echo %%a    ^| %%b
)
echo ---------------------------------------------

endlocal

if /i "%1" neq "/nopause" pause