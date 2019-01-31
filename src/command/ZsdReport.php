<?php
/**
 * Created by PhpStorm.
 * User: 57458
 * Date: 2019/1/31
 * Time: 12:26
 */

namespace evan\think\sms\command;

use evan\think\sms\driver\Zsd;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class ZsdReport extends Command
{

    protected function configure()
    {
        $this->setName('sms:zsd-report')
            ->addArgument('count', Argument::OPTIONAL, '获取报告的数量, 默认1000', 1000)
            ->setDescription('众视达短信报告: php think sms:zsd-report {1000?}');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int|null|void
     * @throws \evan\think\sms\exception\ZSDNetworkErrorException
     * @throws \evan\think\sms\exception\ZSDSendErrorException
     */
    protected function execute(Input $input, Output $output)
    {
        $count = $input->getArgument('count');
        Zsd::getBizInstance()->report($count);
    }
}