<?php
/**
 * Created by PhpStorm.
 * User: zhangguangming
 * Date: 2018/4/18
 * Time: 上午9:39
 */

namespace Gmzhang\Interactive;


use GuzzleHttp\Client;

class InteractiveClient
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var int
     */
    protected $id;


    public $scenes = [];

    /**
     * InteractiveClient constructor.
     * @param array $config
     */
    public function __construct(array $config, $id = '')
    {
        $this->client = new Client([
            'base_uri' => $config['base_uri'],
            'headers' => [
                'Authorization' => 'Token ' . $config['token'],
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->id = $id;
    }

    /**
     * @param $rtmpAddr
     * @return array
     * @throws \Exception
     */
    public function create($rtmpAddr)
    {
        $params = ['rtmp_addr' => $rtmpAddr];
        $ret = $this->client->post('', ['json' => $params]);
        if ($ret->getStatusCode() != 200) throw new \Exception("create interactive fail.", $ret->getStatusCode());
        $ret = json_decode((string)$ret->getBody(), true);
        $this->id = $ret['id'];
        return $ret;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        $ret = $this->client->delete($this->id . "/");
        if ($ret->getStatusCode() != 204) throw new \Exception("delete interactive fail.", $ret->getStatusCode());
        return true;
    }

    /**
     * 获取互动内容
     * @return mixed
     */
    public function getInteractive()
    {
        $ret = $this->client->get($this->id . '/')->getBody();
        return json_decode((string)$ret, true);
    }

    /**
     * 查询混流任务是否在运行
     * @return bool
     */
    public function getMixSwitchStatus()
    {
        $ret = $this->client->get($this->id . '/mix_switch/')->getBody();
        $ret = json_decode((string)$ret, true);
        return (bool)$ret['mix_status'];
    }

    /**
     * 开启混流
     * @param $count
     * @return array
     * @throws \Exception
     */
    public function startMixSwitch($count)
    {
        $params = ['mix_count' => $count];
        $ret = $this->client->post($this->id . '/mix_switch/', ['json' => $params]);
        if ($ret->getStatusCode() != 200) throw new \Exception("start mix switch fail.", $ret->getStatusCode());
        $ret = json_decode((string)$ret->getBody(), true);
        return $ret;
    }

    /**
     * 关闭混流
     * @return bool
     * @throws \Exception
     */
    public function stopMixSwitch()
    {
        $ret = $this->client->delete($this->id . '/mix_switch/');
        if ($ret->getStatusCode() != 204) throw new \Exception("stop mix switch fail.", $ret->getStatusCode());
        return true;
    }

    /**
     * 获取场景配置
     * @return array
     */
    public function getScenes()
    {
        $ret = $this->client->get($this->id . '/mix_config/')->getBody();
        $ret = json_decode((string)$ret, true);
        return $ret;
    }

    /**
     * 更新场景配置
     * @param $config
     * @return bool
     * @throws \Exception
     */
    public function updateScenes($config)
    {
        $params = $config;
        $ret = $this->client->put($this->id . '/mix_config/', ['json' => $params]);
        if ($ret->getStatusCode() != 204) throw new \Exception("update scenes config fail.", $ret->getStatusCode());
        return true;
    }

    /**
     * 获取所有MRTC地址及状态
     * @return array
     */
    public function getStreams()
    {
        $ret = $this->client->get($this->id . '/streams/')->getBody();
        $ret = json_decode((string)$ret, true);
        return $ret;
    }


}