<?php
/**
 * Created by PhpStorm.
 * User: 57458
 * Date: 2019/1/31
 * Time: 11:42
 */

namespace evan\think\sms;


use evan\think\sms\driver\Zsd;
use evan\think\sms\exception\SmsClientTypErrorException;

class Sender
{

    /**
     * @param int $type client类型 1-验证码 2-通知短信 3-营销短信 4-灰度营销短信
     * @return Zsd|null
     * @throws \Exception
     */
    private static function getClient($type = 1)
    {
        $driver = config('sms.driver');
        switch ($driver){
            case 'zsd':
                switch ($type){
                    case 1: // 验证码短信
                    case 2: // 行业通知短信
                        $client = Zsd::getBizInstance();
                        break;
                    case 3: // 营销短信
                        $client = Zsd::getMarketInstance();
                        break;
                    case 4: // 灰度营销短信
                        $client = Zsd::getGrayMarketInstance();
                        break;
                    default:
                        throw new \Exception('Client类型错误');
                }
                break;
            default:
                throw new \Exception('不支持的驱动类型');
        }
        return $client;
    }

    /**
     * 发送验证码短信
     * @param $mobile
     * @param $content
     * @param $signName
     * @return model\ZsdSmsRecord
     * @throws exception\ZSDNetworkErrorException
     * @throws exception\ZSDSendErrorException
     * @throws \Exception
     */
    public static function sendVerifyCodeSms($mobile, $content, $signName)
    {
        return self::getClient(1)->send($mobile, $content, $signName);
    }

    /**
     * 发送业务通知短信
     * @param $mobile
     * @param $content
     * @param $signName
     * @return model\ZsdSmsRecord
     * @throws exception\ZSDNetworkErrorException
     * @throws exception\ZSDSendErrorException
     * @throws \Exception
     */
    public static function sendNotifySms($mobile, $content, $signName)
    {
        return self::getClient(2)->send($mobile, $content, $signName);
    }

    /**
     * 发送营销短信
     * @param string|array $mobile
     * @param $content
     * @param $signName
     * @return model\ZsdSmsRecord
     * @throws exception\ZSDNetworkErrorException
     * @throws exception\ZSDSendErrorException
     * @throws \Exception
     */
    public static function sendMarketingSms($mobile, $content, $signName)
    {
        return self::getClient(3)->send($mobile, $content, $signName);
    }

    /**
     * 发送灰度营销短信
     * @param $mobile
     * @param $content
     * @param $signName
     * @return model\ZsdSmsRecord
     * @throws exception\ZSDNetworkErrorException
     * @throws exception\ZSDSendErrorException
     * @throws \Exception
     */
    public static function sendGrayMarketingSms($mobile, $content, $signName)
    {
        return self::getClient(4)->send($mobile, $content, $signName);
    }
}