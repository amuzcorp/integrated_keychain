<?php
namespace Amuz\XePlugin\IntegratedKeychain;

class IntegratedKeychainService
{
    private $unique_keys;
    private $key_chains;
    private $config;
    private $keychainConfig;

    public function __construct(){
        $this->config = app('xe.config');
        $this->keychainConfig = $this->config->get('integrated_keychain');
        if($this->keychainConfig == null){
            $this->config->set('integrated_keychain', ['keychain' => []]);
            $this->keychainConfig = $this->config->get('integrated_keychain');
        }

        $this->unique_keys = null;
        $this->key_chains = null;
    }

    public function getConfig(){
        return $this->keychainConfig;
    }

    public function getValueById($key_id){
        return array_get($this->getKeyById($key_id),'value');
    }

    public function getKeyByID($key_id){
        $unique_keys = $this->getUniqueKeys();
        if($unique_keys == null) return false;

        $filtered_array = array_where($unique_keys, function ($value) use ($key_id){
            return array_get($value,'requester.' . $key_id);
        });

        return array_first($filtered_array);
    }

    public function getUniqueKeys(){
        if($this->unique_keys == null) $this->setUniqueKeys();
        return $this->unique_keys;
    }

    public function getKeyChains(){
        if($this->key_chains == null) $this->setUniqueKeys();
        return $this->key_chains;
    }

    public function setUniqueKeys(){
        $registered_keychains = \XeRegister::get('integrated_keychain') ?: [];
        if(count($registered_keychains) < 1) return null;

        $pluginHandler = app('xe.plugin');
        $key_chains = [];
        $unique_keys = [];

        foreach($registered_keychains as $pluginId => $keychain){
            foreach($keychain as $requestId => $key){
                //키의 고유 아이디를 생성
                $key_id = sprintf('%s_%s',array_get($key,'vid','vendor'),array_get($key,'pid','product'));

                //현재 요청자를 정리
                $requester = [
                    'id' => $pluginId,
                    'title' => $pluginHandler->getPlugin($pluginId)->getTitle(),
                    'requestId' => $requestId,
                    'requestPlugin' => $pluginId,
                    'how' => array_get($key,'how',''),
                ];

                //다른 플러그인에서 먼저 등록한 적이 있는지 확인
                $exist_key = array_get($unique_keys,$key_id);
                //이미 등록되어있다면, 각 플러그인에서 지시한 ordering에 따라 우선순위 부여 (DESC방식)
                //이 우선순위는 탭,그룹의 구분일 뿐 값은 똑같이 가져올 수 있도록 설계한다.
                if($exist_key != null){
                    if(array_get($exist_key,'ordering',1) >= array_get($key,'ordering',1)){
                        if(!isset($exist_key['requester'])) $exist_key['requester'] = [];
                        $exist_key['requester'][$requestId] = $requester;
                        $unique_keys[$key_id] = $exist_key;
                        continue;
                    }
                }

                //유니크 키로 등록
                $unique_key = [
                    '_type' => array_get($key,'type','formText'),
                    '_args' => [
                        'name'=> $key_id,
                        'label'=>array_get($key,'label','제목 없음'),
                        'description'=>array_get($key,'description'),
                        'placeholder' => array_get($key,'placeholder'),
                        'value' => $this->keychainConfig->get('keychain.' . $key_id) ?: array_get($key,'default'),
                        'options' => array_get($key,'options'),
                    ],
                    'value' => $this->keychainConfig->get('keychain.' . $key_id) ?: array_get($key,'default'),
                    'id' => $key_id,
                    'vid' => array_get($key,'vid','vendor'),
                    'pid' => array_get($key,'pid','product'),
                    'requester' => [
                        $requestId => $requester
                    ],
                ];
                $unique_keys[$key_id] = $unique_key;

                //등록된 유니크 키를 탭/그룹단위로 정리
                $group = sprintf('%s.%s.%s',array_get($key,'tab','기본'),array_get($key,'group','기본'),$key_id);
                array_set($key_chains,$group,$unique_key);
            }
        }
        $this->unique_keys = $unique_keys;
        $this->key_chains = $key_chains;
    }
}
