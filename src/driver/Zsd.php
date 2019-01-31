<?php
/**
 * Created by PhpStorm.
 * User: 57458
 * Date: 2018/11/22
 * Time: 17:00
 */

namespace evan\think\sms\driver;


use evan\think\sms\exception\ZSDNetworkErrorException;
use evan\think\sms\exception\ZSDSendErrorException;
use evan\think\sms\model\ZsdSmsItem;
use evan\think\sms\model\ZsdSmsRecord;
use GuzzleHttp\Client as HttpClient;
use think\facade\Log;

/**
 * 新版众视达短信
 * Class ZSDNew
 * @package App\Sms\Driver
 */
class Zsd
{
    // 短信请求状态(对应每次请求的全部号码)
    public static $statusMsgs = [
        '1' =>  '消息包格式错误',
        '2' =>  'IP鉴权错误',
        '3' =>  '账号密码不正确',
        '4' =>  '版本号错误',
        '5' =>  '其它错误',
        '6' =>  '接入点错误（如账户本身开的是CMPP接入）',
        '7' =>  '账号状态异常（账号已停用）',
        '8' =>  '号码不能为空',
        '9' =>  '内容不能为空',
        '21' =>  '连接过多',
        '100' =>  '系统内部错误，一般情况下例如：提交手机号码为 电信，但是该账号没用可用的电信接出点',
        '102' =>  '单次提交的号码数过多（建议200以内）',
        '0' =>  '鉴权成功',
    ];

    // 短信发送结果(对应每个手机号)
    public static $resultMsgs = [
        '10' => '原发号码错误，即extno错误',
        '15' => '余额不足',
        '17' => '账号签名无效',
        '0' => '提交成功',
    ];

    // 短信接收状态(对应每个手机号)
    public static $statMsgs = [
        'DELIVRD' =>'短信投递成功',
        'EXPIRED' =>'Message validity period has expired',
        'DELETED' =>'Message has been deleted.',
        'REJECTED' =>'Message is in a rejected state',
        'MA:0001' =>'全局黑名单号码',
        'MA:0002' =>'内容非法',
        'MA:0003' =>'无法找到下级路由',
        'MA:0004' =>'未知',
        'MA:0005' =>'目的号码格式错误',
        'MA:0006' =>'系统拒绝',
        'MA:0009' =>'未定义错误',
        'MA:0011' =>'未知系统内部错误',
        'MA:0012' =>'防钓鱼',
        'MA:0013' =>'非法错误的包时序',
        'MA:0014' =>'非法的OP_ISDN号段',
        'MA:0021' =>'号码格式错误',
        'MA:0022' =>'号码超过半小时下发次数限制',
        'MA:0023' =>'客户黑名单号码',
        'MA:0024' =>'内容未报备',
        'MA:0025' =>'不支持该短信',
        'MA:0026' =>'分条发送，组包超时',
        'MA:0027' =>'通道黑名单',
        'MA:0028' =>'全局黑名单号段',
        'MA:0029' =>'通道黑名单号段',
        'MA:0030' =>'直接产生拒绝报告',
        'MO:200' =>'不支持分条短信',
        'MO:0254' =>'转发提交超时',
        'MO:0255' =>'转发提交过程中，连接断开',
        'MO:NNNN' =>'NNNN为对外提交过程中，上级网关的返回值，具体含义需上级网关解释',
    ];

    protected $account;
    protected $password;
    protected $extno;

    /**
     * ZSDNew constructor.
     * @param $account string 账号
     * @param $password string 密码
     * @param $extno string 接入号
     */
    protected function __construct($account, $password, $extno)
    {
        $this->account = $account;
        $this->password = $password;
        $this->extno = $extno;
    }

    /**
     * 获取业务短信实例
     * @return Zsd
     */
    public static function getBizInstance()
    {
        $config = config('sms.zsd.biz');
        return new static($config['account'], $config['password'], $config['extno']);
    }

    /**
     * 获取正规营销短信实例
     * @return Zsd
     */
    public static function getMarketInstance()
    {
        $config = config('sms.zsd.marketing');
        return new static($config['account'], $config['password'], $config['extno']);
    }

    /**
     * 获取非正规营销短信实例
     * @return Zsd
     */
    public static function getGrayMarketInstance()
    {
        $config = config('sms.zsd.gray_marketing');
        return new static($config['account'], $config['password'], $config['extno']);
    }

    /**
     * 发送短信
     * @param array|string $mobile
     * @param $content
     * @param $signName
     * @return ZsdSmsRecord
     * @throws ZSDNetworkErrorException
     * @throws ZSDSendErrorException
     */
    public function send($mobile, $content, $signName)
    {
        if(is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }
        // 添加短信记录
        $url = 'http://120.24.247.128:7862/sms';
        $record = new ZsdSmsRecord();
        $record->account = $this->account;
        $record->extno = $this->extno;
        $record->content = $content;
        $record->sign_name = $signName;
        $record->mobile = $mobile;
        $result = self::request($url, [
            'action' => 'send',
            'account' => $this->account,
            'password' => $this->password,
            'mobile' => $mobile,
            'content' => "【{$signName}】$content",
            'extno' => $this->extno,
            'rt' => 'json',
        ]);
        $record->status = $result['status'];
        $statusDesc = isset(self::$statusMsgs[$result['status']]) ? self::$statusMsgs[$result['status']] : '未知错误码';
        $record->status_desc = $statusDesc;
        $record->balance = $result['balance'];
        $record->save();

        if($result['status'] != '0'){
            Log::error('请求众视达失败, 返回信息: ', $result);
            throw new ZSDSendErrorException($statusDesc);
        }

        $list = $result['list'];
        foreach ($list as $item) {
            $smsItem = new ZsdSmsItem();
            $smsItem->record_id = $record->id;
            $smsItem->mid = $item['mid'];
            $smsItem->mobile = $item['mobile'];
            $smsItem->result = $item['result'];
            $smsItem->result_desc = self::$resultMsgs[$item['result']];
            $smsItem->save();
        }

        return $record;
    }

    /**
     * 获取短信状态报告
     * @param int $pageSize
     * @throws ZSDNetworkErrorException
     * @throws ZSDSendErrorException
     */
    public function report($pageSize = 1000)
    {

        $url = 'http://120.24.247.128:7862/sms';

        $result = self::request($url, [
            'action' => 'report',
            'account' => $this->account,
            'password' => $this->password,
            'size' => $pageSize,
            'rt' => 'json',
        ]);

        if($result['status'] != '0'){
            throw new ZSDSendErrorException(self::$statusMsgs[$result['status']]);
        }
        $list = $result['list'];
        foreach ($list as $item) {
            $smsItem = ZsdSmsItem::where('mid', $item['mid'])->get();
            $smsItem->spid = $item['spid'];
            $smsItem->access_code = $item['accessCode'];
            $smsItem->stat = $item['stat'];
            $smsItem->stat_desc = self::$statMsgs[$item['stat']];
            $smsItem->report_time = $item['time'];
            $smsItem->save();
        }
    }

    /**
     * @param $url
     * @param $data
     * @return array|string
     * @throws ZSDNetworkErrorException
     */
    private static function request($url, $data)
    {
        $client = new HttpClient();
        $response = $client->post($url, [
            'form_params' => $data
        ]);
        if($response->getStatusCode() != 200){
            throw new ZSDNetworkErrorException('网络错误, 请求众视达通道失败');
        }
        $result = $response->getBody()->getContents();
        $result = json_decode($result, 1);
        return $result;
    }
}