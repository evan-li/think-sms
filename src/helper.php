<?php
// 注册命令
\think\Console::addDefaultCommands([
    \evan\think\sms\command\ZsdReport::class,
]);