@echo off
chcp 65001 >nul
setlocal EnableExtensions EnableDelayedExpansion

set "ROOT=%~dp0"
if "%ROOT:~-1%"=="\" set "ROOT=%ROOT:~0,-1%"

set "MODE=%~1"
if not defined MODE set "MODE=all"

set "CONFIG=%ROOT%\deploy-test\deploy\config.json"
set "READER=%ROOT%\deploy-test\read-config.ps1"

if not exist "%CONFIG%" (
    echo [ERROR] Не найден config: %CONFIG%
    exit /b 1
)

if not exist "%READER%" (
    echo [ERROR] Не найден read-config.ps1: %READER%
    exit /b 1
)

set "ENV_FILE=%TEMP%\theme_deploy_%RANDOM%%RANDOM%.bat"
powershell -NoProfile -ExecutionPolicy Bypass -File "%READER%" -ConfigPath "%CONFIG%" -OutPath "%ENV_FILE%" >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Не удалось прочитать %CONFIG%
    exit /b 1
)

call "%ENV_FILE%"
del "%ENV_FILE%" >nul 2>&1

if not defined BRANCH set "BRANCH=main"
if not defined SSH_PORT set "SSH_PORT=22"

if /I "%MODE%"=="push" goto do_push
if /I "%MODE%"=="upload" goto do_upload
if /I "%MODE%"=="all" goto do_all

echo [ERROR] Неизвестный режим: %MODE%
echo Использование: deploy-theme.bat [push^|upload^|all]
exit /b 1

:do_all
call :push_theme || exit /b 1
call :upload_theme || exit /b 1
echo [OK] Полный деплой темы завершен.
exit /b 0

:do_push
call :push_theme || exit /b 1
echo [OK] Push темы завершен.
exit /b 0

:do_upload
call :upload_theme || exit /b 1
echo [OK] Загрузка темы на сервер завершена.
exit /b 0

:push_theme
if not defined REPO_URL (
    echo [ERROR] В конфиге не задан REPO_URL.
    exit /b 1
)

git -C "%ROOT%" rev-parse --is-inside-work-tree >nul 2>&1
if errorlevel 1 (
    echo [ERROR] В %ROOT% не инициализирован git.
    exit /b 1
)

git -C "%ROOT%" remote get-url origin >nul 2>&1
if errorlevel 1 (
    git -C "%ROOT%" remote add origin "%REPO_URL%"
) else (
    for /f "usebackq delims=" %%u in (`git -C "%ROOT%" remote get-url origin`) do set "CUR_REMOTE=%%u"
    if /I not "!CUR_REMOTE!"=="%REPO_URL%" git -C "%ROOT%" remote set-url origin "%REPO_URL%"
)

git -C "%ROOT%" add .
git -C "%ROOT%" commit -m "Theme deploy %date% %time%" >nul 2>&1
git -C "%ROOT%" push -u origin "%BRANCH%"
if errorlevel 1 exit /b 1
exit /b 0

:upload_theme
if not defined SSH_HOST (
    echo [ERROR] В конфиге не задан SSH_HOST.
    exit /b 1
)
if not defined SSH_USER (
    echo [ERROR] В конфиге не задан SSH_USER.
    exit /b 1
)
if not defined REMOTE_PATH (
    echo [ERROR] В конфиге не задан REMOTE_PATH.
    exit /b 1
)

where ssh >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Не найден ssh.
    exit /b 1
)
where scp >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Не найден scp.
    exit /b 1
)

if defined SSH_KEY_PATH (
    if exist "%SSH_KEY_PATH%" (
        set "SSH_KEY_ARG=-i \"%SSH_KEY_PATH%\""
    )
)

if not defined SSH_KEY_ARG (
    echo [WARN] SSH_KEY_PATH не задан или файл не найден. Будет использован стандартный ssh-agent/ключи.
    set "SSH_KEY_ARG="
)

echo Создаем директорию на сервере...
ssh %SSH_KEY_ARG% -p %SSH_PORT% -o StrictHostKeyChecking=accept-new "%SSH_USER%@%SSH_HOST%" "mkdir -p \"%REMOTE_PATH%\""
if errorlevel 1 exit /b 1

echo Загружаем файлы темы на сервер (без .git и deploy-test)...
for /f "delims=" %%i in ('dir /b /a "%ROOT%"') do (
    if /I not "%%i"==".git" if /I not "%%i"=="deploy-test" (
        scp %SSH_KEY_ARG% -P %SSH_PORT% -o StrictHostKeyChecking=accept-new -r "%ROOT%\%%i" "%SSH_USER%@%SSH_HOST%:%REMOTE_PATH%/"
        if errorlevel 1 exit /b 1
    )
)

exit /b 0